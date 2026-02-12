<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\UpdateUserInfoService;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\GenerateTokenService;
use Backend\Domain\Entities\User; // Add this line to import the User class
use Backend\Application\InsertProxyLoginService;
use Backend\Infrastructure\ProxyLoginRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$userName =$data['user_name'] ?? '';
$userEmail = $data['user_email'] ?? '';
$userRoleId = $data['user_role_id'] ?? '';
$toUserId = $data['to_user_id'] ?? '';
$toUserName = $data['to_user_name'] ?? '';
$toUserEmail = $data['to_user_email'] ?? '';
$toBoxUserId = $data['to_box_user_id'] ?? '';
$toBoxAccessToken = $data['to_box_access_token'] ?? '';
$toBoxRefreshToken = $data['to_box_refresh_token'] ?? '';

$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$userName || !$userEmail)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました UserName:".$userName,
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
    'message'  => "ユーザー認証でエラーが出ました invalid user info\nuserId:".$userInfo['userId'],
  ]);
  exit;
}

// 管理者権限のチェック
if ($userRoleId !== "admin"){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました admin権限でないと実行できません",
  ]);
  exit;
}
// fromユーザー情報取得
$fromUser = $userInfoService->GetUserInfo($userInfo['userId']);
$toUser = $userInfoService->GetUserInfo($toUserId);
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

// 出来上がりToken返却

// successチェック
//if (!$res['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => true,
    'message'  => "Proxy login token created successfully.",
    'access_token'  => $token['accessToken'],
    'refresh_token' => $token['refreshToken'],
    'expire_at'     => $token['expireAt'],
  ]);
//}

