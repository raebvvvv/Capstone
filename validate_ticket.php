<?php
require __DIR__ . '/config.php';

// Public validator page. Accepts GET ?code=... (minimal response) or ?code=...&t=... (full details with signed token).
// Uses submissions table as the source of truth.

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');

// Simple session-based rate limiting to deter brute force (tunable)
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$now = time();
$_SESSION['vt_hits'] = array_filter((array)($_SESSION['vt_hits'] ?? []), function($ts) use ($now){ return ($now - (int)$ts) < 60; });
$_SESSION['vt_hits'][] = $now;
if (count($_SESSION['vt_hits']) > 60) {
  // Too many requests this minute; respond with generic message
  http_response_code(429);
  echo '<!doctype html><meta charset="utf-8"><title>Validate Ticket</title><div style="font-family:Arial,sans-serif;padding:24px;">Please try again later.</div>';
  exit;
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function b64u_decode($s){ $r = strtr($s, '-_', '+/'); return base64_decode($r . str_repeat('=', (4 - strlen($r) % 4) % 4)); }

$code  = trim((string)($_GET['code'] ?? ''));
$token = trim((string)($_GET['t'] ?? ''));

$found = false; $ticket = null; $error = '';
$tokenValid = false;

if ($code !== '') {
  try {
    $stmt = $pdo->prepare("SELECT submission_code, first_name, middle_name, last_name, status, created_at FROM submissions WHERE submission_code = ? LIMIT 1");
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
      $dateFmt = '';
      if (!empty($row['created_at'])) {
        try { $dateFmt = date('F d, Y', strtotime($row['created_at'])); } catch (Throwable $e) { $dateFmt = (string)$row['created_at']; }
      }
      $ticket = [
        'code' => (string)$row['submission_code'],
        'name' => $fullName,
        'date' => $dateFmt,
        'status' => (string)($row['status'] ?? ''),
      ];
    } else {
      $error = 'Invalid';
    }
  } catch (Throwable $e) {
    $error = 'Server error';
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
            <h3 class="mb-3">Ticket Validation</h3>
            <form method="get" class="row g-2 mb-3" action="<?php echo h('validate_ticket.php'); ?>">
              <div class="col-sm-9">
                <input type="text" name="code" class="form-control" placeholder="Enter Request ID (e.g. SRID-2025-20250929-1 or ERID-2025-20250929-1)" value="<?php echo h($code); ?>" required>
              </div>
              <div class="col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Validate</button>
              </div>
            </form>

            <?php if ($code !== ''): ?>
              <?php if ($found): ?>
                <?php if ($tokenValid): ?>
                  <div class="alert alert-success">Valid</div>
                  <dl class="row mb-0">
                    <dt class="col-sm-4">Request ID</dt><dd class="col-sm-8"><?php echo h($ticket['code']); ?></dd>
                    <dt class="col-sm-4">Name</dt><dd class="col-sm-8"><?php echo h($ticket['name']); ?></dd>
                    <dt class="col-sm-4">Date</dt><dd class="col-sm-8"><?php echo h($ticket['date']); ?></dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><?php echo h($ticket['status']); ?></dd>
                  </dl>
                <?php else: ?>
                  <div class="alert alert-success">Valid</div>
                  <p class="text-muted mb-0 small">Details hidden. To view details, scan the official QR or use a signed link.</p>
                <?php endif; ?>
              <?php else: ?>
                <div class="alert alert-danger">Invalid</div>
              <?php endif; ?>
            <?php else: ?>
              <div class="text-muted">Enter a Request ID above and click Validate, or use a URL like:<br>
                <code><?php echo h(rtrim(BASE_URL, '/')); ?>/validate_ticket.php?code=SRID-2025-20250929-1</code> (students) or
                <code><?php echo h(rtrim(BASE_URL, '/')); ?>/validate_ticket.php?code=ERID-2025-20250929-1</code> (employees)
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
