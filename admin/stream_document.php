<?php
require __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';

// Admin-only secure stream of submission document by document_id or type
if (empty($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
    http_response_code(403); exit('Forbidden');
}

$submissionId = (int)($_GET['id'] ?? 0);
$docId = (int)($_GET['did'] ?? 0);
$type = trim((string)($_GET['type'] ?? ''));

if ($submissionId <= 0) { http_response_code(400); exit('Bad request'); }

try {
    $sql = 'SELECT document_id, file_path, mime_type FROM submission_documents WHERE submission_id = ?';
    $args = [$submissionId];
    if ($docId > 0) { $sql .= ' AND document_id = ?'; $args[] = $docId; }
    elseif ($type !== '') { $sql .= ' AND doc_type = ?'; $args[] = $type; }
    $sql .= ' ORDER BY document_id ASC LIMIT 1';
    $s = $pdo->prepare($sql);
    $s->execute($args);
    $row = $s->fetch(PDO::FETCH_ASSOC);
    if (!$row) { http_response_code(404); exit('File not found'); }

    $filename = basename((string)$row['file_path']);
    $fs = storage_path('uploads' . DIRECTORY_SEPARATOR . $filename);
    if (!is_file($fs)) { http_response_code(404); exit('Missing'); }

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
}
