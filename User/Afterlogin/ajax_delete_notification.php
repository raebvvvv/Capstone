<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try{
  if(!isset($_SESSION['user_id'])){ http_response_code(401); echo json_encode(['success'=>false,'message'=>'unauthenticated']); exit; }
  $user_id = (int)$_SESSION['user_id'];
  $id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
  if($id <= 0){ http_response_code(400); echo json_encode(['success'=>false,'message'=>'invalid_id']); exit; }
  // Reuse admin delete logic but scoped to user
  $check = $pdo->prepare('SELECT deleted_at FROM user_notifications WHERE id = ? AND user_id = ? LIMIT 1');
  $check->execute([$id, $user_id]);
  $existing = $check->fetch(PDO::FETCH_ASSOC);
  // Permanently delete (user requested behavior)
  $del = $pdo->prepare('DELETE FROM user_notifications WHERE id = ? AND user_id = ?');
  $del->execute([$id, $user_id]);
  $deleted = $del->rowCount();
  $st2 = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st2->execute([$user_id]); $row = $st2->fetch(PDO::FETCH_ASSOC); $count = $row ? (int)$row['cnt'] : 0;
  echo json_encode(['success'=>true,'deleted'=>$deleted,'unread'=>$count]);
}catch(Throwable $e){ http_response_code(500); echo json_encode(['success'=>false,'message'=>'server_error']); }
