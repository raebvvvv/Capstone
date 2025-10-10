<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

// CSV export of missing files
if (isset($_GET['export']) && $_GET['export'] === 'missing') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="missing_uploads_' . date('Ymd_His') . '.csv"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Request ID','Title','Doc Type','Filename (DB)','Expected Path','Exists']);

    $stmt = $pdo->query("SELECT sd.submission_id, sd.doc_type, sd.file_path, s.submission_code, s.title, s.created_at
                         FROM submission_documents sd
                         JOIN submissions s ON s.submission_id = sd.submission_id
                         ORDER BY s.created_at DESC, sd.doc_type ASC, sd.file_path ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $filename = basename((string)$row['file_path']);
        $safe = trim(str_replace(["\r","\n"], '', $filename));
    $fsPath = storage_path('uploads/' . $safe);
        $exists = is_file($fsPath) ? 'yes' : 'no';
        if ($exists === 'no') {
            fputcsv($out, [
                (string)$row['submission_code'],
                (string)$row['title'],
                (string)$row['doc_type'],
                $safe,
                $fsPath,
                $exists,
            ]);
        }
    }
    fclose($out);
    exit;
}

// Fetch and compute stats
$rows = [];
$present = 0; $missing = 0;
try {
    $stmt = $pdo->query("SELECT sd.submission_id, sd.doc_type, sd.file_path, s.submission_code, s.title, s.created_at
                         FROM submission_documents sd
                         JOIN submissions s ON s.submission_id = sd.submission_id
                         ORDER BY s.created_at DESC, sd.doc_type ASC, sd.file_path ASC");
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $filename = basename((string)$r['file_path']);
        $safe = trim(str_replace(["\r","\n"], '', $filename));
    $fsPath = storage_path('uploads/' . $safe);
        $exists = is_file($fsPath);
        $rows[] = [
            'request_id' => (string)$r['submission_code'],
            'title' => (string)$r['title'],
            'doc_type' => (string)$r['doc_type'],
            'filename' => $safe,
            'path' => $fsPath,
            'exists' => $exists,
        ];
        if ($exists) $present++; else $missing++;
    }
} catch (Throwable $e) {
    $rows = [];
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Uploads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; }
        .badge-present { background: #198754; }
        .badge-missing { background: #dc3545; }
        code { user-select: all; }
    </style>
 </head>
<body class="p-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Verify Uploads</h1>
            <div>
                <a class="btn btn-sm btn-outline-secondary" href="completed_applications.php">Back</a>
                <a class="btn btn-sm btn-danger" href="?export=missing">Export Missing (CSV)</a>
            </div>
        </div>
        <div class="mb-3">
            <span class="badge badge-present">Present: <?php echo (int)$present; ?></span>
            <span class="badge badge-missing ms-2">Missing: <?php echo (int)$missing; ?></span>
            <div class="small text-muted mt-2">Root folder checked: <code><?php echo htmlspecialchars(storage_path('uploads/')); ?></code></div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Title</th>
                        <th>Doc Type</th>
                        <th>Filename (DB)</th>
                        <th>Exists</th>
                        <th>Path</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                            <td class="text-truncate" style="max-width: 420px;" title="<?php echo htmlspecialchars($row['title']); ?>"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['doc_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['filename']); ?></td>
                            <td>
                                <?php if ($row['exists']): ?>
                                    <span class="badge badge-present">present</span>
                                <?php else: ?>
                                    <span class="badge badge-missing">missing</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><code><?php echo htmlspecialchars($row['path']); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3 small text-muted">
            Tip: If many files show missing, check if the <code>uploads/</code> folder was cleaned or moved, and that <code>submission_documents.file_path</code> stores only the filename (no subfolders). Filenames are sanitized (CR/LF trimmed) before checks.
        </div>
    </div>
</body>
</html>
