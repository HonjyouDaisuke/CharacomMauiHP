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
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました",
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
    'message'  => "ユーザー認証でエラーが出ました: ".($userInfo['message'] ?? 'invalid token'),
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

$usecase = new GetUserInfoService($db, $config);

$res = $usecase->GetAllUsers();

if ($res === null) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー情報取得でエラーになりました。",
  ]);
  exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'users'  => $res,
]);
