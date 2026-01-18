<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\UpdateUserInfoService;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$userId =$data['user_id'] ?? '';
$userRoleId = $data['user_role_id'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$userId || !$userRoleId)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました UserId:".$userId,
  ]);
  exit;
} 

// DBインスタンス
$db = new Database($config);
$crypto = new OpenSSLEncryptionService(
    base64_decode($config['enc_key']),  // decode して 32 バイトに
    base64_decode($config['enc_iv'])   // decode して 16 バイトに
);

// User認証 & User情報取得
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);
if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => "ユーザー認証でエラーが出ました: ".($userInfo['message'] ?? 'invalid token'),
  ]);
  exit;
}
$executeUser = $userInfoService->GetUserInfo($userInfo['userId']);
if (!$executeUser || $executeUser->role_id != "admin"){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました admin権限でないと実行できません",
  ]);
  exit;
}
$userRepo = new UserRepository($db, $crypto);
$usecase = new UpdateUserInfoService($userRepo);

$res = $usecase->updateUserRole($userId, $userRoleId);

if (!$res['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => $res['message'] ?? "ユーザー権限変更でエラーになりました。",
  ]);
  exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'message'  => $res['message']."userRole=".$executeUser->role_id,
]);
