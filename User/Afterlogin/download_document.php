<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

// Stream a user-owned submission document securely from storage, not webroot.
// GET params: id (submission_id or code), type (doc_type) OR file (filename) with validation.

if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); exit; }

$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) { http_response_code(401); exit('Unauthorized'); }

$subCode = trim((string)($_GET['code'] ?? ''));
$subId = (int)($_GET['id'] ?? 0);
$docType = trim((string)($_GET['type'] ?? ''));

try {
    if ($subId <= 0 && $subCode !== '') {
        $q = $pdo->prepare('SELECT submission_id FROM submissions WHERE submission_code = ? AND user_id = ? LIMIT 1');
        $q->execute([$subCode, $userId]);
        $subId = (int)($q->fetchColumn() ?: 0);
    } else if ($subId > 0) {
        // Verify ownership
        $q = $pdo->prepare('SELECT submission_id FROM submissions WHERE submission_id = ? AND user_id = ? LIMIT 1');
        $q->execute([$subId, $userId]);
        if (!$q->fetchColumn()) { http_response_code(403); exit('Forbidden'); }
    }
    if ($subId <= 0) { http_response_code(404); exit('Submission not found'); }

    $args = [];
    $sql = 'SELECT file_path, mime_type FROM submission_documents WHERE submission_id = ?';
    $args[] = $subId;
    if ($docType !== '') { $sql .= ' AND doc_type = ?'; $args[] = $docType; }
    $sql .= ' ORDER BY document_id ASC LIMIT 1';
    $s = $pdo->prepare($sql);
    $s->execute($args);
    $row = $s->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); exit('File not found'); }

    $filename = basename((string)$row['file_path']);
    $fs = storage_path('uploads' . DIRECTORY_SEPARATOR . $filename);
    if (!is_file($fs)) { http_response_code(404); exit('File missing'); }

    $mime = $row['mime_type'] ?: 'application/pdf';
    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    readfile($fs);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    exit;
}
