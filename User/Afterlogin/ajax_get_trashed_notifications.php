<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try{
  if(!isset($_SESSION['user_id'])){ http_response_code(401); echo json_encode(['success'=>false,'message'=>'unauthenticated']); exit; }
  $user_id = (int)$_SESSION['user_id'];
  $st = $pdo->prepare('SELECT id, title, message, meta, is_read, created_at, deleted_at FROM user_notifications WHERE user_id = ? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC LIMIT 200');
  $st->execute([$user_id]); $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: []; foreach($rows as &$r){ $r['meta'] = $r['meta'] ? json_decode($r['meta'], true) : null; }
  echo json_encode(['success'=>true,'notifications'=>$rows]);
}catch(Throwable $e){ http_response_code(500); echo json_encode(['success'=>false,'message'=>'server_error']); }
