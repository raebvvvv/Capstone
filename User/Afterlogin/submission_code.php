<?php
// filepath: c:\xampp\htdocs\Capstone\User\Afterlogin\submission_code.php
require __DIR__ . '/../../conn.php';

// Get all distinct dates with missing codes
$dates = $pdo->query("SELECT DISTINCT DATE(created_at) as dt FROM submissions WHERE submission_code IS NULL OR submission_code = ''")->fetchAll(PDO::FETCH_COLUMN);

foreach ($dates as $dt) {
    // Get all submissions for this date, ordered by submission_id (or created_at)
    $rows = $pdo->prepare("SELECT submission_id, created_at FROM submissions WHERE DATE(created_at) = ? AND (submission_code IS NULL OR submission_code = '') ORDER BY created_at ASC, submission_id ASC");
    $rows->execute([$dt]);
    $submissions = $rows->fetchAll(PDO::FETCH_ASSOC);

    $today_code = date('Ymd', strtotime($dt));
    foreach ($submissions as $i => $row) {
        $count = $i + 1;
        $code = sprintf('SRID-%s-%d', date('Y', strtotime($dt)).'-'.$today_code, $count);
        // Make sure this code is not already used
        $exists = $pdo->prepare("SELECT COUNT(*) FROM submissions WHERE submission_code = ?");
        $exists->execute([$code]);
        if ($exists->fetchColumn() > 0) {
            // If code exists, increment count until unique
            do {
                $count++;
                $code = sprintf('SRID-%s-%d', date('Y', strtotime($dt)).'-'.$today_code, $count);
                $exists->execute([$code]);
            } while ($exists->fetchColumn() > 0);
        }
        $update = $pdo->prepare("UPDATE submissions SET submission_code = ? WHERE submission_id = ?");
        $update->execute([$code, $row['submission_id']]);
    }
}
echo "Backfill complete!";
?>