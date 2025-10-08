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
 *  - output_dir (string) Absolute path where generated certificates are stored (defaults to uploads/certificates)
 *  - title_font_size (int) Default 18
 *  - authors_font_size (int) Default 12
 *  - date_font_size (int) Default 11
 *  - coords (array) Override coordinates; keys: title, authors, date each => ['x'=>..,'y'=>..,'w'=>..]
 *  - line_height (float) Multicell line height (default 6)
 *  - debug_boxes (bool) If true, draws bounding boxes around each text block to aid positioning
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
        // 1. Load submission basics
        $stmt = $pdo->prepare("SELECT submission_id, submission_code, title, status, status_updated_at, created_at, user_id FROM submissions WHERE submission_id = ? LIMIT 1");
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
        if (empty($authors)) {
            // Fallback: attempt to derive submitter's profile name if authors table empty
            try {
                $pstmt = $pdo->prepare("SELECT u.role, sp.first_name AS sf, sp.middle_name AS sm, sp.last_name AS sl, ep.first_name AS ef, ep.middle_name AS em, ep.last_name AS el FROM users u LEFT JOIN student_profiles sp ON u.user_id=sp.user_id LEFT JOIN employee_profiles ep ON u.user_id=ep.user_id WHERE u.user_id = ?");
                $pstmt->execute([(int)$sub['user_id']]);
                $p = $pstmt->fetch(PDO::FETCH_ASSOC) ?: [];
                $fn = $p['sf'] ?? $p['ef'] ?? '';
                $mn = $p['sm'] ?? $p['em'] ?? '';
                $ln = $p['sl'] ?? $p['el'] ?? '';
                $mi = $mn ? (' ' . strtoupper(mb_substr($mn, 0, 1)) . '.') : '';
                $fallbackName = trim($fn . $mi . ' ' . $ln);
                if ($fallbackName !== '') { $authors[] = $fallbackName; }
            } catch (Throwable $e) { /* ignore */ }
        }

        $title = trim((string)($sub['title'] ?? 'Untitled Work'));
        if ($title === '') { $title = 'Untitled Work'; }
        $authorsLine = implode(', ', $authors);
        $dateTs = $sub['status_updated_at'] ? strtotime($sub['status_updated_at']) : ($sub['created_at'] ? strtotime($sub['created_at']) : time());
        $datePretty = date('F j, Y', $dateTs ?: time());

        // 3. Paths & caching
        $template = $opts['template'] ?? app_path('admin/New-Certificate.pdf');
        if (!is_file($template)) { throw new RuntimeException('Template PDF missing at ' . $template); }
        $outputDir = $opts['output_dir'] ?? app_path('uploads/certificates');
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
        $titleFontSize   = (int)($opts['title_font_size'] ?? 18);
        $authorsFontSize = (int)($opts['authors_font_size'] ?? 12);
        $dateFontSize    = (int)($opts['date_font_size'] ?? 11);
        $lineHeight      = (float)($opts['line_height'] ?? 6.0);
        $coords = $opts['coords'] ?? [];
        // Default coordinates (mm) — placeholder guesses; adjust after first visual test
        $titlePos   = $coords['title']   ?? ['x'=>50,'y'=>110,'w'=>170];
        $authorsPos = $coords['authors'] ?? ['x'=>20,'y'=>130,'w'=>170];
        $datePos    = $coords['date']    ?? ['x'=>20,'y'=>150,'w'=>170];

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

        // Build PDF
        $pdf = new Fpdi();
        $pdf->setSourceFile($template);
        $tplIdx = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplIdx);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
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
        $drawBlock($pdf, $authorsOut,'Helvetica','',$authorsFontSize,$authorsPos,'C',$lineHeight,[0,0,0],$debug,[0,0,255]);
        // Date block
        $drawBlock($pdf, $dateOut,'Helvetica','',$dateFontSize,$datePos,'C',$lineHeight,[0,0,0],$debug,[0,128,0]);

        // Save to disk atomically
        $tmp = $targetPath . '.tmp';
        $pdf->Output($tmp, 'F');
        @rename($tmp, $targetPath);

        if (function_exists('log_event')) {
            log_event('CERT_GENERATED', 'Certificate generated', [ 'submission_id'=>$submissionId, 'file'=>$targetPath ]);
        }
        return $targetPath;
    }
}
