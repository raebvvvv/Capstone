<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();
verify_csrf_post();

$user_ids = isset($_POST['user_ids']) && is_array($_POST['user_ids']) ? array_map('intval', $_POST['user_ids']) : [];
$action = isset($_POST['bulk_action']) ? $_POST['bulk_action'] : '';
$scope = isset($_POST['status_scope']) ? $_POST['status_scope'] : '';

if (!$user_ids || !$action) {
    header('Location: manageuser.php');
    exit();
}

// Prevent self-admin from deleting/deactivating own account
$adminId = $_SESSION['user_id'] ?? 0;
$user_ids = array_values(array_filter($user_ids, function($id) use ($adminId) { return $id !== (int)$adminId; }));
if (!$user_ids) {
    header('Location: manageuser.php');
    exit();
}

$in = implode(',', array_fill(0, count($user_ids), '?'));

try {
    if ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id IN ($in)");
        $stmt->execute($user_ids);
    } elseif ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE user_id IN ($in)");
        $stmt->execute($user_ids);
    } elseif ($action === 'delete') {
        // Hard delete; remove profiles first then users within a transaction
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("DELETE FROM student_profiles WHERE user_id IN ($in)");
            $stmt->execute($user_ids);
            $stmt = $pdo->prepare("DELETE FROM employee_profiles WHERE user_id IN ($in)");
            $stmt->execute($user_ids);
            $stmt = $pdo->prepare("DELETE FROM admin_profiles WHERE user_id IN ($in)");
            $stmt->execute($user_ids);
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id IN ($in)");
            $stmt->execute($user_ids);
            $pdo->commit();
        } catch (Exception $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }
} catch (Exception $e) {
    // Optionally log error
}

header('Location: manageuser.php');
exit();
