<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'unauthenticated']);
        exit;
    }
    $user_id = (int)$_SESSION['user_id'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'invalid_id']);
        exit;
    }

    // First, try to mark an admin notification (admins use the same endpoint). If none updated, try user notifications.
    $adminMarked = 0;
    try {
        $stAdmin = $pdo->prepare('UPDATE admin_notifications SET is_read = 1 WHERE id = ? AND is_read = 0');
        $stAdmin->execute([$id]);
        $adminMarked = $stAdmin->rowCount();
    } catch (Throwable $e) {
        // ignore if admin_notifications table doesn't exist or other issues; we'll try user_notifications next
        $adminMarked = 0;
    }

    $userMarked = 0;
    if ($adminMarked === 0) {
            $st = $pdo->prepare('UPDATE user_notifications SET is_read = 1 WHERE id = ? AND user_id = ? AND is_read = 0 AND deleted_at IS NULL');
            $st->execute([$id, $user_id]);
        $userMarked = $st->rowCount();
    }

    // return updated unread count for the current user (useful for client badge updates)
    $st2 = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
    $st2->execute([$user_id]);
    $row = $st2->fetch(PDO::FETCH_ASSOC);
    $count = $row ? (int)$row['cnt'] : 0;

    echo json_encode(['success' => true, 'unread' => $count, 'marked_admin' => $adminMarked, 'marked_user' => $userMarked]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'server_error']);
}
