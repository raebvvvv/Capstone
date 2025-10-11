<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try{
  if(!isset($_SESSION['user_id'])){ http_response_code(401); echo json_encode(['success'=>false,'message'=>'unauthenticated']); exit; }
  $user_id = (int)$_SESSION['user_id'];
  $ids = [];
  if(isset($_POST['ids'])){
    $raw = $_POST['ids'];
    if(is_string($raw)) $raw = json_decode($raw, true);
    if(is_array($raw)) $ids = array_map('intval', $raw);
  }
  if(empty($ids)) { echo json_encode(['success'=>false,'message'=>'no_ids']); exit; }
  $placeholders = implode(',', array_fill(0, count($ids), '?'));
  $params = $ids; array_unshift($params, $user_id);
  // Only allow deleting rows belonging to the user
  $sql = "DELETE FROM user_notifications WHERE user_id = ? AND id IN ($placeholders)";
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $deleted = $st->rowCount();
  echo json_encode(['success'=>true,'deleted'=>$deleted]);
}catch(Throwable $e){ http_response_code(500); echo json_encode(['success'=>false,'message'=>'server_error']); }
