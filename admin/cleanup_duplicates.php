<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('require_admin')) { require_admin(); }
header('Content-Type: application/json; charset=utf-8');
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success'=>false,'message'=>'POST required']);
        exit;
    }
    // Fetch all non-deleted notifications
    $stmt = $pdo->prepare('SELECT id, user_id, title, meta, created_at FROM user_notifications WHERE deleted_at IS NULL ORDER BY user_id, created_at DESC');
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group by user_id + title + submission_id (if present in meta). Keep newest id per group.
    $keep = [];
    $toDelete = [];
    foreach($rows as $r){
        $submissionId = '';
        if(!empty($r['meta'])){
            $m = json_decode($r['meta'], true);
            if(is_array($m) && isset($m['submission_id'])) $submissionId = (string)$m['submission_id'];
        }
        $key = $r['user_id'] . '||' . $r['title'] . '||' . $submissionId;
        if(!isset($keep[$key])){
            // first encountered (rows ordered by created_at DESC) -> keep
            $keep[$key] = (int)$r['id'];
        } else {
            // older duplicate -> mark for soft-delete
            $toDelete[] = (int)$r['id'];
        }
    }

    if(empty($toDelete)){
        echo json_encode(['success'=>true,'message'=>'No duplicates found','deleted'=>0]);
        exit;
    }

    // Soft-delete marked ids
    $place = implode(',', array_fill(0, count($toDelete), '?'));
    $upd = $pdo->prepare('UPDATE user_notifications SET deleted_at = NOW() WHERE id IN (' . $place . ') AND deleted_at IS NULL');
    $upd->execute($toDelete);
    $deleted = $upd->rowCount();

    echo json_encode(['success'=>true,'deleted'=>$deleted,'attempted'=>count($toDelete)]);
} catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>substr($e->getMessage(),0,200)]);
}
