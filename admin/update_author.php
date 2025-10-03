<?php
// Admin: Update a single author in submission_authors
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');
if (function_exists('verify_csrf_header')) { verify_csrf_header(); }
if (function_exists('require_admin')) { require_admin(); }

try {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $authorId = isset($input['author_id']) ? (int)$input['author_id'] : 0;
    $first = trim((string)($input['first_name'] ?? ''));
    $middle = trim((string)($input['middle_name'] ?? ''));
    $last = trim((string)($input['last_name'] ?? ''));
    $studentId = trim((string)($input['student_id'] ?? ''));
    $mobile = trim((string)($input['mobile'] ?? ''));
    $address = trim((string)($input['home_address'] ?? ''));
    $webmail = trim((string)($input['webmail'] ?? ''));

    if ($authorId <= 0) {
        echo json_encode(['success'=>false,'error'=>'Invalid author_id']);
        exit;
    }

    // Reuse normalization similar to submit-form
    $norm = function($s){
        $s = trim((string)$s);
        if ($s==='') return '';
        $s = strtolower(preg_replace('/\s+/', ' ', $s));
        $s = preg_replace_callback('/\b([a-z])/', function($m){ return strtoupper($m[1]); }, $s);
        $s = preg_replace_callback('/-([a-z])/', function($m){ return '-'.strtoupper($m[1]); }, $s);
        $s = preg_replace_callback("/'([a-z])", function($m){ return "'".strtoupper($m[1]); }, $s);
        return $s;
    };
    $first = $norm($first);
    $middle = $norm($middle);
    $last = $norm($last);

    $sql = 'UPDATE submission_authors SET first_name = ?, middle_name = ?, last_name = ?, student_id = ?, mobile = ?, home_address = ?, webmail = ? WHERE author_id = ?';
    try {
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([$first,$middle,$last,$studentId,$mobile,$address,$webmail,$authorId]);
    } catch (Throwable $e) {
        $msg = $e->getMessage();
        // Fallback 1: middle_name column may not exist
        if (stripos($msg, 'unknown column') !== false && stripos($msg, 'middle_name') !== false) {
            $sql2 = 'UPDATE submission_authors SET first_name = ?, last_name = ?, student_id = ?, mobile = ?, home_address = ?, webmail = ? WHERE author_id = ?';
            $stmt = $pdo->prepare($sql2);
            $ok = $stmt->execute([$first,$last,$studentId,$mobile,$address,$webmail,$authorId]);
        } elseif (stripos($msg, 'unknown column') !== false && stripos($msg, 'author_id') !== false) {
            // Fallback 2: PK might be named `id`
            $sql3 = 'UPDATE submission_authors SET first_name = ?, middle_name = ?, last_name = ?, student_id = ?, mobile = ?, home_address = ?, webmail = ? WHERE id = ?';
            $stmt = $pdo->prepare($sql3);
            $ok = $stmt->execute([$first,$middle,$last,$studentId,$mobile,$address,$webmail,$authorId]);
        } else {
            throw $e;
        }
    }
    if ($ok) {
        if (function_exists('log_event')) { log_event('EDIT_AUTHOR', 'Author updated', ['author_id'=>$authorId]); }
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'error'=>'DB error']);
    }
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Update author failed', ['err'=>$e->getMessage()]); }
    echo json_encode(['success'=>false,'error'=>'Operation failed','detail'=>$e->getMessage()]);
}
?>
