<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\UpdateUserInfoService;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\GenerateTokenService;
use Backend\Domain\Entities\User;
use Backend\Application\InsertProxyLoginService;
use Backend\Infrastructure\ProxyLoginRepository;
use Backend\Application\DeleteProxyLoginService;

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
    'message'  => "入力チェックでエラーが出ました token:".$token,
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
$fromUserId = $userInfo['payload']['from_user_id'] ?? '';
if (!$userInfo['success'] || !$fromUserId) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました invalid user info\nuserId:".$userInfo['userId']." fromUserId:".$fromUserId,
  ]);
  exit;
}

// proxy中かどうかのチェック
if (!$userInfo['isProxy']){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "Proxyログイン中ではありません。",
  ]);
  exit;
}

// fromユーザー情報取得
$fromUser = $userInfoService->GetUserInfo($fromUserId);
if (!$fromUser) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "fromユーザー情報取得でエラーが出ました fromUserId:".$fromUserId,
  ]);
  exit;
}

/// Proxyログイン情報削除
$proxyRepo = new ProxyLoginRepository($db, $crypto);
$deleteService = new DeleteProxyLoginService($proxyRepo);
$success = $deleteService->execute($fromUserId);
if (!$success['success']){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "Proxyログイン情報削除でエラーが出ました: ".($success['message'] ?? ''),
  ]);
  exit;
}

$getTokenService = new GenerateTokenService($config['jwt_secret']);
$token = $getTokenService->execute($fromUser, false, "");

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'message'  => "Proxy logout. token created successfully.",
  'access_token'  => $token['accessToken'],
  'refresh_token' => $token['refreshToken'],
  'expire_at'     => $token['expireAt'],
]);


