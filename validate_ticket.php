<?php
require __DIR__ . '/config.php';

// Public validator page. Accepts GET ?code=... (minimal response) or ?code=...&t=... (full details with signed token).
// Uses submissions table as the source of truth.

// Security headers (page-specific; global headers may also be applied via security_bootstrap)
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Permissions-Policy: geolocation=(), camera=(), microphone=(), interest-cohort=()');

// Simple session-based rate limiting to deter brute force (tunable)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$now = time();
$_SESSION['vt_hits'] = array_filter((array)($_SESSION['vt_hits'] ?? []), function($ts) use ($now){ return ($now - (int)$ts) < 60; });
$_SESSION['vt_hits'][] = $now;
// Lightweight IP-based limiter alongside session-based limiter
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$_SESSION['vt_ip_hits'] = $_SESSION['vt_ip_hits'] ?? [];
$_SESSION['vt_ip_hits'][$clientIp] = array_filter((array)($_SESSION['vt_ip_hits'][$clientIp] ?? []), function($ts) use ($now){ return ($now - (int)$ts) < 60; });
$_SESSION['vt_ip_hits'][$clientIp][] = $now;
if (count($_SESSION['vt_hits']) > 60 || count($_SESSION['vt_ip_hits'][$clientIp]) > 60) {
  // Too many requests this minute; respond with generic message
  http_response_code(429);
  echo '<!doctype html><meta charset="utf-8"><title>Validate Ticket</title><div style="font-family:Arial,sans-serif;padding:24px;">Please try again later.</div>';
  exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function b64u_decode($s){ $r = strtr($s, '-_', '+/'); return base64_decode($r . str_repeat('=', (4 - strlen($r) % 4) % 4)); }

$code  = trim((string)($_GET['code'] ?? ''));
$token = trim((string)($_GET['t'] ?? ''));
$key   = trim((string)($_GET['k'] ?? ''));

// Resolve opaque key if provided: maps to { code, token } stored server-side by the certificate generator
if ($key !== '') {
  if (preg_match('/^[a-f0-9]{24}$/', $key) === 1) {
    $recPath = storage_path('qr_links' . DIRECTORY_SEPARATOR . $key . '.json');
    if (is_readable($recPath)) {
      $json = (string)@file_get_contents($recPath);
      $rec  = json_decode($json, true);
      if (is_array($rec)) {
        $code  = (string)($rec['c'] ?? $code);
        $token = (string)($rec['t'] ?? $token);
        // optional: enforce exp on the link record as well
        if (isset($rec['exp']) && (int)$rec['exp'] < time()) {
          $code = '';
          $token = '';
        }
      }
  // Keep the key file so the printed QR remains reusable until expiration
    }
  } else {
    // Invalid key format: ignore
    $key = '';
  }
}

// Normalize and validate inputs early
// Allow typical formats like SRID-YYYY-YYYYMMDD-N and ERID-YYYY-YYYYMMDD-N
$isCodeValid = false;
if ($code !== '') {
  if (
    preg_match('/^(SRID|ERID)-\d{4}-\d{8}-\d{1,6}$/', $code) === 1
    || preg_match('/^[A-Z0-9\-]{5,64}$/', $code) === 1 // fallback: conservative allow-list, prevents injection chars
  ) {
    $isCodeValid = true;
  }
}
// Cap token length and charset to safe Base64URL + '.' separator
if ($token !== '' && (strlen($token) > 2048 || preg_match('/^[A-Za-z0-9._\-]{10,2048}$/', $token) !== 1)) {
  $token = '';
}

$found = false; $ticket = null; $error = '';
$tokenValid = false;

if ($code !== '' && $isCodeValid) {
  try {
    $stmt = $pdo->prepare("
        SELECT 
            s.submission_code, 
            s.status, 
            s.created_at,
        s.reviewed_at,
        s.reviewer_id,
        CASE 
          WHEN u.role = 'student' THEN CONCAT_WS(' ', sp.first_name, sp.middle_name, sp.last_name)
          WHEN u.role = 'employee' THEN CONCAT_WS(' ', ep.first_name, ep.middle_name, ep.last_name)
          ELSE 'Unknown User'
        END as full_name,
        CASE 
          WHEN u.role = 'student' THEN sp.first_name
          WHEN u.role = 'employee' THEN ep.first_name
          ELSE 'Unknown'
        END as first_name,
        CASE 
          WHEN u.role = 'student' THEN sp.middle_name
          WHEN u.role = 'employee' THEN ep.middle_name
          ELSE ''
        END as middle_name,
        CASE 
          WHEN u.role = 'student' THEN sp.last_name
          WHEN u.role = 'employee' THEN ep.last_name
          ELSE 'User'
        END as last_name
        FROM submissions s
        LEFT JOIN users u ON u.user_id = s.user_id
        LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
        LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
        WHERE s.submission_code = ? LIMIT 1");
    $stmt->execute([$code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      $found = true;
      // If a token is present, validate signature and expiry
      if ($token && strpos($token, '.') !== false) {
        [$pB64, $sB64] = explode('.', $token, 2);
        $calc = hash_hmac('sha256', $pB64, TICKET_SIGNING_KEY, true);
        $calcB64 = rtrim(strtr(base64_encode($calc), '+/', '-_'), '=');
        if (hash_equals($calcB64, $sB64)) {
          $payload = json_decode(b64u_decode($pB64), true);
          if ($payload && ($payload['c'] ?? '') === $code) {
            if (!isset($payload['exp']) || (int)$payload['exp'] >= time()) {
              $tokenValid = true;
            }
          }
        }
      }

      // Prepare details (used only when $tokenValid)
      $fullName = trim(trim(($row['first_name'] ?? '')) . ' ' . trim(($row['middle_name'] ?? '')) . ' ' . trim(($row['last_name'] ?? '')));
      // Prefer reviewed_at if available for display date
      $dateFmt = '';
      $dateSrc = !empty($row['reviewed_at']) ? $row['reviewed_at'] : ($row['created_at'] ?? '');
      if (!empty($dateSrc)) {
        try { $dateFmt = date('F d, Y', strtotime($dateSrc)); } catch (Throwable $e) { $dateFmt = (string)$dateSrc; }
      }
      // Resolve reviewer/admin who completed the ticket
      $reviewerName = '';
      $rid = isset($row['reviewer_id']) ? (int)$row['reviewer_id'] : 0;
      if ($rid > 0) {
        try {
          $rStmt = $pdo->prepare('SELECT first_name, last_name FROM admin_profiles WHERE profile_id = ? LIMIT 1');
          if ($rStmt && $rStmt->execute([$rid])) {
            if ($r = $rStmt->fetch(PDO::FETCH_ASSOC)) {
              $fn = trim((string)($r['first_name'] ?? ''));
              $ln = trim((string)($r['last_name'] ?? ''));
              $nm = trim($fn . ' ' . $ln);
              if ($nm !== '') { $reviewerName = $nm; }
            }
          }
        } catch (Throwable $e1) { /* ignore */ }
        if ($reviewerName === '') {
          try {
            $uStmt = $pdo->prepare('SELECT sp.first_name AS sf, sp.last_name AS sl, ep.first_name AS ef, ep.last_name AS el FROM users u LEFT JOIN student_profiles sp ON u.user_id=sp.user_id LEFT JOIN employee_profiles ep ON u.user_id=ep.user_id WHERE u.user_id = ? LIMIT 1');
            if ($uStmt && $uStmt->execute([$rid])) {
              $u = $uStmt->fetch(PDO::FETCH_ASSOC) ?: [];
              $fn = $u['sf'] ?? $u['ef'] ?? '';
              $ln = $u['sl'] ?? $u['el'] ?? '';
              $nm = trim($fn . ' ' . $ln);
              if ($nm !== '') { $reviewerName = $nm; }
            }
          } catch (Throwable $e2) { /* ignore */ }
        }
      }
      $ticket = [
        'code' => (string)$row['submission_code'],
        'name' => $fullName,
        'date' => $dateFmt,
        'reviewed_by' => $reviewerName,
      ];
    } else {
      $error = 'Invalid';
    }
  } catch (Throwable $e) {
    // Avoid leaking server details
    $error = 'Invalid';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Validate Ticket</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="mb-3">Verification Result</h3>

            <?php if ($code !== ''): ?>
              <?php if ($found): ?>
                <?php if ($tokenValid): ?>
                  <div class="alert alert-success">The certificate is valid.</div>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">Request ID</dt><dd class="col-sm-8"><?php echo h($ticket['code']); ?></dd>
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?php echo h($ticket['name']); ?></dd>
                    <dt class="col-sm-4">Date</dt><dd class="col-sm-8"><?php echo h($ticket['date']); ?></dd>
                    <?php if (!empty($ticket['reviewed_by'])): ?>
                      <dt class="col-sm-4">Completed by</dt><dd class="col-sm-8"><?php echo h($ticket['reviewed_by']); ?></dd>
                    <?php endif; ?>
                  </dl>
                <?php else: ?>
                  <div class="alert alert-success">Valid</div>
                  <p class="text-muted mb-0 small">Details hidden. To view details, scan the official QR or use a signed link.</p>
                <?php endif; ?>
              <?php else: ?>
                <div class="alert alert-danger">Invalid</div>
              <?php endif; ?>
            <?php else: ?>
              <div class="text-muted small">Scan the official QR printed on the certificate to view verification details.</div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>