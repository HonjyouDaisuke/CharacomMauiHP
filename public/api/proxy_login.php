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

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$toUserId = $data['to_user_id'] ?? '';

$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました.",
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
    'message'  => "ユーザー認証でエラーが出ました.",
  ]);
  exit;
}

// 管理者権限のチェック（サーバーサイドで取得したロールを使用）
$fromUser = $userInfoService->GetUserInfo($userInfo['userId']);
$toUser = $userInfoService->GetUserInfo($toUserId);

if (!$fromUser || !$toUser) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー情報の取得に失敗しました。",
  ]);
  exit;
}

if (!$fromUser || $fromUser->role_id !== "admin"){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました admin権限でないと実行できません",
  ]);
  exit;
}

// toユーザー情報を上書き
$toUser->box_user_id = $fromUser->box_user_id;
$toUser->box_access_token = $fromUser->box_access_token;
$toUser->box_refresh_token = $fromUser->box_refresh_token;

$getTokenService = new GenerateTokenService($config['jwt_secret']);
$token = $getTokenService->execute($toUser, true, $fromUser->id);

// proxyテーブル作成
$proxyLoginRepo = new ProxyLoginRepository($db, $crypto);

// Insert proxy login record
$insertProxyLoginService = new InsertProxyLoginService($proxyLoginRepo);
$res = $insertProxyLoginService->execute($fromUser, $toUser);

// successチェック
if (!$res['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "Proxyログイン情報作成でエラーが出ました: ".($res['message'] ?? ''),
  ]);
  exit;
}

// 出来上がりToken返却
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'message'  => "Proxy login token created successfully.",
  'access_token'  => $token['accessToken'],
  'refresh_token' => $token['refreshToken'],
  'expire_at'     => $token['expireAt'],
]);


