<?php
/**
 * Certificate Generator
 * Uses FPDI + existing template (admin/New-Certificate.pdf) to stamp dynamic fields.
 *
 * Public API:
 *   generate_certificate(PDO $pdo, int $submissionId, array $opts = []): string
 *     Returns absolute filesystem path to generated PDF.
 *     Re-uses cached file unless force_regen option supplied or underlying data changed.
 *
 * Options:
 *  - force_regen (bool) Force regeneration even if cached file exists.
 *  - template (string) Absolute path to template PDF (defaults to admin/New-Certificate.pdf)
 *  - output_dir (string) Absolute path where generated certificates are stored (defaults to storage_path('certificates'))
 *  - title_font_size (int) Default 18
 *  - authors_font_size (int) Default 12
 *  - date_font_size (int) Default 11
 *  - coords (array) Override coordinates; keys: title, authors, date each => ['x'=>..,'y'=>..,'w'=>..]
 *  - line_height (float) Multicell line height (default 6)
 *  - debug_boxes (bool) If true, draws bounding boxes around each text block to aid positioning
 *  - qr (array|bool) If truthy, add a QR code linking to public validator with signed token. When array, accepts:
 *      - x, y, size (mm) or w,h
 *      - caption (string) Optional small text below QR
 *      - ttl_seconds (int) Token validity window, default 90 days
 *
 * Coordinate System:
 *   FPDI/FPDF units default to millimeters (A4 ~ 210x297). Adjust as needed; measure positions using a PDF viewer
 *   or by temporarily drawing bounding boxes (see DEBUG section below).
 */

// Ensure composer autoload is loaded (adjust path if project root differs)
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (is_file(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// If composer install not yet run, the class below will be missing.
// After running: composer install
// vendor/autoload.php will load setasign\Fpdi\Fpdi.
use setasign\Fpdi\Fpdi; // provided by setasign/fpdi in composer.json

if (!class_exists(Fpdi::class)) {
    // Provide an actionable error early (instead of silent fatal later)
    trigger_error("FPDI library not loaded. Run 'composer install' in project root (where composer.json is) to install setasign/fpdi & setasign/fpdf.", E_USER_WARNING);
}

if (!function_exists('generate_certificate')) {
    function generate_certificate(PDO $pdo, int $submissionId, array $opts = []): string {
        // 1. Load submission basics (schema no longer includes completed_by/completed_at)
        $stmt = $pdo->prepare("SELECT submission_id, submission_code, title, status, status_updated_at, created_at, user_id FROM submissions WHERE submission_id = ? LIMIT 1");
        $stmt = $pdo->prepare("SELECT submission_id, submission_code, title, status, status_updated_at, created_at, user_id, reviewer_id, reviewed_at FROM submissions WHERE submission_id = ? LIMIT 1");
        $stmt->execute([$submissionId]);
        $sub = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$sub) {
            throw new RuntimeException('Submission not found');
        }
        // Only generate for completed (business rule); relax if needed
        $status = strtolower((string)($sub['status'] ?? ''));
        if ($status !== 'completed') {
            throw new RuntimeException('Submission not completed yet');
        }

        // 2. Authors list (include adviser among authors; sort adviser first)
        $authStmt = $pdo->prepare("SELECT first_name, middle_name, last_name, is_adviser FROM submission_authors WHERE submission_id = ? ORDER BY is_adviser DESC, last_name ASC, first_name ASC");
        $authStmt->execute([$submissionId]);
        $authors = [];
        while ($row = $authStmt->fetch(PDO::FETCH_ASSOC)) {
            $fn = trim((string)($row['first_name'] ?? ''));
            $mn = trim((string)($row['middle_name'] ?? ''));
            $ln = trim((string)($row['last_name'] ?? ''));
            $mi = $mn !== '' ? (' ' . strtoupper(mb_substr($mn, 0, 1)) . '.') : '';
            $full = trim($fn . $mi . ' ' . $ln);
            if ($full === '') continue;
            $authors[] = $full;
        }
        // Deduplicate while preserving order
        $authors = array_values(array_unique($authors));
        // Derive submitter name now so we can ensure inclusion even if authors already present
        $submitterName = '';
        try {
            $pstmt = $pdo->prepare("SELECT u.role, sp.first_name AS sf, sp.middle_name AS sm, sp.last_name AS sl, ep.first_name AS ef, ep.middle_name AS em, ep.last_name AS el FROM users u LEFT JOIN student_profiles sp ON u.user_id=sp.user_id LEFT JOIN employee_profiles ep ON u.user_id=ep.user_id WHERE u.user_id = ?");
            $pstmt->execute([(int)$sub['user_id']]);
            $p = $pstmt->fetch(PDO::FETCH_ASSOC) ?: [];
            $fn = $p['sf'] ?? $p['ef'] ?? '';
            $mn = $p['sm'] ?? $p['em'] ?? '';
            $ln = $p['sl'] ?? $p['el'] ?? '';
            $mi = $mn ? (' ' . strtoupper(mb_substr($mn, 0, 1)) . '.') : '';
            $submitterName = trim($fn . $mi . ' ' . $ln);
        } catch (Throwable $e) { /* ignore */ }
        if (empty($authors)) {
            // Fallback: attempt to derive submitter's profile name if authors table empty
            if ($submitterName !== '') { $authors[] = $submitterName; }
        } else {
            // Ensure submitter is present if not already (case-insensitive comparison)
            if ($submitterName !== '') {
                $lowerAuthors = array_map('mb_strtolower', $authors);
                if (!in_array(mb_strtolower($submitterName), $lowerAuthors, true)) {
                    $authors[] = $submitterName; // append at end
                }
            }
        }

        $title = trim((string)($sub['title'] ?? 'Untitled Work'));
        if ($title === '') { $title = 'Untitled Work'; }
        $authorsLine = implode(', ', $authors);
        $dateTs = $sub['status_updated_at'] ? strtotime($sub['status_updated_at']) : ($sub['created_at'] ? strtotime($sub['created_at']) : time());
    // Prefer reviewed_at for certificate date if available, else status_updated_at, else created_at
    $dateTs = $sub['reviewed_at'] ? strtotime($sub['reviewed_at']) : ($sub['status_updated_at'] ? strtotime($sub['status_updated_at']) : ($sub['created_at'] ? strtotime($sub['created_at']) : time()));
    $datePretty = date('F j, Y', $dateTs ?: time());

    // Build signed validation URL (for QR). We'll generate after knowing submission_code.
    $codeVal = (string)$sub['submission_code'];
    $ttl = isset($opts['qr']['ttl_seconds']) ? (int)$opts['qr']['ttl_seconds'] : (90 * 24 * 60 * 60); // 90 days default
    if ($ttl < 3600) { $ttl = 3600; }
    $payload = [ 'c' => $codeVal, 'iat' => time(), 'exp' => time() + $ttl ];
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $pB64 = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    $sig  = hash_hmac('sha256', $pB64, TICKET_SIGNING_KEY, true);
    $sB64 = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');
        $token = $pB64 . '.' . $sB64;
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        // Prefer an opaque short key so code/token are not exposed in the URL. Fallback to explicit params on failure.
        $validateUrl = '';
        try {
            $linksDir = storage_path('qr_links');
            if (!is_dir($linksDir)) { @mkdir($linksDir, 0770, true); }
            // Stable per-code key (24 hex chars) derived from HMAC(code)
            $key = substr(bin2hex(hash_hmac('sha256', $codeVal, TICKET_SIGNING_KEY, true)), 0, 24);
            $payloadRec = [
                'c'   => $codeVal,
                't'   => $token,
                'iat' => time(),
                'exp' => time() + $ttl,
            ];
            $recPath = rtrim($linksDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $key . '.json';
            $ok = (bool)file_put_contents($recPath, json_encode($payloadRec, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            if ($ok) {
                $validateUrl = ($base !== '' ? $base : '') . '/validate_ticket.php?k=' . rawurlencode($key);
            }
        } catch (Throwable $e) { /* ignore and fallback */ }
        if ($validateUrl === '') {
            $validateUrl = ($base !== '' ? $base : '') . '/validate_ticket.php?code=' . rawurlencode($codeVal) . '&t=' . rawurlencode($token);
        }

        // 3. Paths & caching
        $template = $opts['template'] ?? app_path('admin/New-Certificate.pdf');
        if (!is_file($template)) { throw new RuntimeException('Template PDF missing at ' . $template); }
    // Store generated certificates under secure storage outside webroot by default
    $outputDir = $opts['output_dir'] ?? storage_path('certificates');
        if (!is_dir($outputDir)) { @mkdir($outputDir, 0775, true); }
        $codeSlug = preg_replace('/[^A-Za-z0-9_-]+/','_', (string)$sub['submission_code']);
        $targetPath = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $codeSlug . '.pdf';

        $force = !empty($opts['force_regen']);
        // Regenerate if force OR file missing OR template newer OR data newer than file
        $needsRegen = $force || !is_file($targetPath);
        if (!$needsRegen) {
            $tplMtime = @filemtime($template) ?: 0;
            $fileMtime = @filemtime($targetPath) ?: 0;
            $dataMtime = max(strtotime($sub['status_updated_at'] ?? '') ?: 0, strtotime($sub['created_at'] ?? '') ?: 0);
            if ($tplMtime > $fileMtime || $dataMtime > $fileMtime) { $needsRegen = true; }
        }
        if (!$needsRegen) {
            return $targetPath; // Cached
        }

        // 4. PDF Composition
        $titleFontSize   = (int)($opts['title_font_size'] ?? 15);
    $authorsFontSize = (int)($opts['authors_font_size'] ?? 12);
    $authorsAlign    = strtoupper($opts['authors_align'] ?? 'L'); // default left; allow override 'C' or 'R'
    if (!in_array($authorsAlign, ['L','C','R'], true)) { $authorsAlign = 'L'; }
        $dateFontSize    = (int)($opts['date_font_size'] ?? 12);
        $lineHeight      = (float)($opts['line_height'] ?? 6.0);
        $coords = $opts['coords'] ?? [];
        // Final default coordinates (mm) — tuned layout
        $titlePos     = $coords['title']     ?? ['x'=>48,'y'=>95,'w'=>180];
        $authorsPos   = $coords['authors']   ?? ['x'=>62.5,'y'=>110,'w'=>180];
        $datePos      = $coords['date']      ?? ['x'=>15,'y'=>163.5,'w'=>120];
        $receiverPos  = $coords['receiver']  ?? ['x'=>113,'y'=>142,'w'=>50]; // area labeled "Name of Receiver/staff from RMIPO"
        $receiverFont = (int)($opts['receiver_font_size'] ?? 15);
    $qrCfg = $opts['qr'] ?? true; // default: enable QR; true to use defaults, or array for config
    // Default QR position/size as per admin preference
    $qrPos = ['x'=>200, 'y'=>140, 'size'=>35];
        $qrCaption = 'Scan to validate';
    $qrDebug = !empty($opts['qr_debug']);
    $qrFallbackText = !empty($opts['qr_fallback_text']);
        if (is_array($qrCfg)) {
            if (isset($qrCfg['x'])) { $qrPos['x'] = (float)$qrCfg['x']; }
            if (isset($qrCfg['y'])) { $qrPos['y'] = (float)$qrCfg['y']; }
            if (isset($qrCfg['size'])) { $qrPos['size'] = (float)$qrCfg['size']; }
            if (isset($qrCfg['w']) && isset($qrCfg['h'])) { $qrPos['size'] = (float)min($qrCfg['w'], $qrCfg['h']); }
            if (!empty($qrCfg['caption']) && is_string($qrCfg['caption'])) { $qrCaption = $qrCfg['caption']; }
        }

        // Safety sanitize strings for FPDF (basic ASCII fallback if needed)
        $sanitize = function(string $s): string {
            // Convert to Windows-1252 subset acceptable by core fonts; fallback transliteration
            if (!mb_check_encoding($s, 'UTF-8')) { $s = mb_convert_encoding($s, 'UTF-8'); }
            // Replace fancy quotes etc.
            $map = ["“"=>'"',"”"=>'"',"‘"=>"'","’"=>"'","–"=>"-","—"=>"-","…"=>'...'];
            $s = strtr($s, $map);
            return $s;
        };
        $titleOut   = $sanitize($title);
        $authorsOut = $sanitize($authorsLine);
        $dateOut    = $sanitize($datePretty);
        // Receiver removed (no completed_by field now); keep variable empty for future use or remove block entirely
        // Derive reviewer (receiver) name from reviewer_id (admin_profiles.profile_id) with fallbacks
        $receiverOut = '';
        if (!empty($sub['reviewer_id'])) {
            $rid = (int)$sub['reviewer_id'];
            // First attempt: admin_profiles by profile_id
            try {
                $rStmt = $pdo->prepare("SELECT first_name, last_name FROM admin_profiles WHERE profile_id = ? LIMIT 1");
                if ($rStmt && $rStmt->execute([$rid])) {
                    if ($row = $rStmt->fetch(PDO::FETCH_ASSOC)) {
                        $fn = trim((string)($row['first_name'] ?? ''));
                        $ln = trim((string)($row['last_name'] ?? ''));
                        $nm = trim($fn . ' ' . $ln);
                        if ($nm !== '') { $receiverOut = $sanitize($nm); }
                    }
                }
            } catch (Throwable $e) { /* ignore */ }
            // Fallback: if still empty, treat reviewer_id as possibly a users.user_id then join student/employee profiles
            if ($receiverOut === '') {
                try {
                    $uStmt = $pdo->prepare("SELECT sp.first_name AS sf, sp.last_name AS sl, ep.first_name AS ef, ep.last_name AS el FROM users u LEFT JOIN student_profiles sp ON u.user_id=sp.user_id LEFT JOIN employee_profiles ep ON u.user_id=ep.user_id WHERE u.user_id = ? LIMIT 1");
                    if ($uStmt && $uStmt->execute([$rid])) {
                        $u = $uStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                        $fn = $u['sf'] ?? $u['ef'] ?? '';
                        $ln = $u['sl'] ?? $u['el'] ?? '';
                        $nm = trim($fn . ' ' . $ln);
                        if ($nm !== '') { $receiverOut = $sanitize($nm); }
                    }
                } catch (Throwable $e2) { /* ignore */ }
            }
        }
    $needsRegen = $force || !is_file($targetPath);
            $dataMtime = max(strtotime($sub['reviewed_at'] ?? '') ?: 0, strtotime($sub['status_updated_at'] ?? '') ?: 0, strtotime($sub['created_at'] ?? '') ?: 0);

        // Build PDF
        $pdf = new Fpdi();
        $pdf->setSourceFile($template);
        $tplIdx = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplIdx);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pageW = (float)$size['width'];
        $pageH = (float)$size['height'];
        $pdf->useTemplate($tplIdx);

        $debug = !empty($opts['debug_boxes']);
        $drawBlock = function(Fpdi $pdf, $text, $fontFamily, $fontStyle, $fontSize, array $pos, $align, $lineHeight, $color, $debug, $boxColorRGB) {
            [$r,$g,$b] = $color;
            $pdf->SetFont($fontFamily, $fontStyle, $fontSize);
            $pdf->SetTextColor($r,$g,$b);
            $pdf->SetXY($pos['x'],$pos['y']);
            $yStart = $pdf->GetY();
            $xStart = $pdf->GetX();
            $pdf->MultiCell($pos['w'],$lineHeight, $text, 0, $align);
            if ($debug) {
                $yEnd = $pdf->GetY();
                $heightUsed = max( $lineHeight, $yEnd - $yStart );
                $pdf->SetDrawColor($boxColorRGB[0],$boxColorRGB[1],$boxColorRGB[2]);
                $pdf->Rect($xStart, $yStart, $pos['w'], $heightUsed);
            }
        };

        // Title block (centered)
        $drawBlock($pdf, $titleOut, 'Helvetica','B',$titleFontSize,$titlePos,'C',$lineHeight,[0,0,0],$debug,[255,0,0]);
        // Authors block
    $drawBlock($pdf, $authorsOut,'Helvetica','',$authorsFontSize,$authorsPos,$authorsAlign,$lineHeight,[0,0,0],$debug,[0,0,255]);
        // Date block
        $drawBlock($pdf, $dateOut,'Helvetica','',$dateFontSize,$datePos,'C',$lineHeight,[0,0,0],$debug,[0,128,0]);
        // (Receiver block omitted since completion admin fields were removed from schema)
        // Receiver (reviewer) block if available
        if ($receiverOut !== '') {
            $drawBlock($pdf, $receiverOut,'Helvetica','',$receiverFont,$receiverPos,'C',$lineHeight,[0,0,0],$debug,[128,0,128]);
        }
    // Clamp QR position to page bounds with a small margin
    $margin = 5.0;
    if ($qrPos['size'] > $pageW - 2*$margin) { $qrPos['size'] = max(10.0, $pageW - 2*$margin); }
    if ($qrPos['size'] > $pageH - 2*$margin) { $qrPos['size'] = max(10.0, $pageH - 2*$margin); }
    $qrPos['x'] = max($margin, min($qrPos['x'], $pageW - $qrPos['size'] - $margin));
    $qrPos['y'] = max($margin, min($qrPos['y'], $pageH - $qrPos['size'] - 2*$margin));

        // QR code (optional)
        $tmpQr = null;
        if ($qrCfg) {
            try {
                $writerClass   = '\\Endroid\\QrCode\\Writer\\PngWriter';
                $qrCodeClass   = '\\Endroid\\QrCode\\QrCode';
                $encodingClass = '\\Endroid\\QrCode\\Encoding\\Encoding';
                $errLevelClass = '\\Endroid\\QrCode\\ErrorCorrectionLevel\\ErrorCorrectionLevelLow';
                if (class_exists($writerClass) && class_exists($qrCodeClass) && class_exists($encodingClass) && class_exists($errLevelClass)) {
                    $qr = $qrCodeClass::create($validateUrl);
                    $encoding = new $encodingClass('UTF-8');
                    $errLevel = new $errLevelClass();
                    $qr = $qr->setEncoding($encoding)
                             ->setErrorCorrectionLevel($errLevel)
                             ->setSize((int)round($qrPos['size'] * 3.7795275591)) // mm to px approx at 96 DPI
                             ->setMargin(2);
                    $writer = new $writerClass();
                    $result = $writer->write($qr);
                    $tmpQr = $targetPath . '.qr.png';
                    $result->saveToFile($tmpQr);
                }
            } catch (Throwable $e) {
                // ignore QR failure, continue without QR
                $tmpQr = null;
            }
        }

        if ($tmpQr && is_file($tmpQr)) {
            // Place QR image
            $pdf->Image($tmpQr, $qrPos['x'], $qrPos['y'], $qrPos['size'], $qrPos['size']);
            if ($qrCaption !== '') {
                $pdf->SetFont('Helvetica','',8);
                $pdf->SetTextColor(0,0,0);
                $pdf->SetXY($qrPos['x'], min($pageH - 8, $qrPos['y'] + $qrPos['size'] + 3));
                $pdf->Cell($qrPos['size'], 4, $qrCaption, 0, 0, 'C');
            }
        } elseif ($qrCfg && class_exists('\\BaconQrCode\\Encoder\\Encoder') && class_exists('\\BaconQrCode\\Common\\ErrorCorrectionLevel')) {
            // Fallback: draw QR modules directly using BaconQrCode (no GD required)
            try {
                $eclClass = '\\BaconQrCode\\Common\\ErrorCorrectionLevel';
                $encClass = '\\BaconQrCode\\Encoder\\Encoder';
                $ecl = $eclClass::L();
                $qrObj = $encClass::encode($validateUrl, $ecl);
                $matrix = $qrObj->getMatrix();
                $w = $matrix->getWidth();
                $h = $matrix->getHeight();
                if ($w > 0 && $h > 0) {
                    $module = min($qrPos['size'] / $w, $qrPos['size'] / $h);
                    $x0 = $qrPos['x'];
                    $y0 = $qrPos['y'];
                    // Draw white background
                    $pdf->SetFillColor(255,255,255);
                    $pdf->Rect($x0, $y0, $module * $w, $module * $h, 'F');
                    // Draw black modules
                    $pdf->SetFillColor(0,0,0);
                    for ($ry=0; $ry<$h; $ry++) {
                        for ($rx=0; $rx<$w; $rx++) {
                            $val = (int)$matrix->get($rx, $ry);
                            if ($val > 0) {
                                $pdf->Rect($x0 + $rx * $module, $y0 + $ry * $module, $module, $module, 'F');
                            }
                        }
                    }
                    if ($qrCaption !== '') {
                        $pdf->SetFont('Helvetica','',8);
                        $pdf->SetTextColor(0,0,0);
                        $pdf->SetXY($x0, $y0 + ($module * $h) + 3);
                        $pdf->Cell($module * $w, 4, $qrCaption, 0, 0, 'C');
                    }
                }
            } catch (Throwable $e) {
                // ignore fallback failure
            }
        } elseif ($qrCfg && ($qrDebug || $qrFallbackText)) {
            // Draw a placeholder box and fallback text (helps verify coordinates even if QR lib missing)
            $pdf->SetDrawColor(150,150,150);
            $pdf->Rect($qrPos['x'], $qrPos['y'], $qrPos['size'], $qrPos['size']);
            $pdf->SetFont('Helvetica','',7);
            $pdf->SetTextColor(80,80,80);
            $pdf->SetXY($qrPos['x'], $qrPos['y'] + $qrPos['size'] + 2);
            $shortUrl = ($base !== '' ? $base : '') . '/validate_ticket.php?code=' . rawurlencode($codeVal);
            $text = $qrCaption !== '' ? $qrCaption : 'Validate';
            $pdf->Cell($qrPos['size'], 3.5, $text, 0, 2, 'C');
            $pdf->SetXY($qrPos['x'], $qrPos['y'] + $qrPos['size'] + 6);
            $pdf->Cell($qrPos['size'], 3.5, $shortUrl, 0, 0, 'C');
        }

        // Save to disk atomically
        $tmp = $targetPath . '.tmp';
        $pdf->Output($tmp, 'F');
        @rename($tmp, $targetPath);

        // Cleanup temp QR
        if ($tmpQr && is_file($tmpQr)) { @unlink($tmpQr); }

        if (function_exists('log_event')) {
            log_event('CERT_GENERATED', 'Certificate generated', [ 'submission_id'=>$submissionId, 'file'=>$targetPath ]);
        }
        return $targetPath;
    }
}