<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Application\DeleteProjectService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserProjectsService;
use Backend\Application\UpdateUserInfoService;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$userName =$data['user_name'] ?? '';
$userEmail = $data['user_email'] ?? '';
$avatarUrl = $data['avatar_url'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$userName || !$userEmail || !$avatarUrl)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました token:".$token." UserName:".$userName,
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
    'message'  => "ユーザー認証でエラーが出ました invalid user info\ntoken=".$token."\n userId:".$userInfo['userId'],
  ]);
  exit;
}

$userRepo = new UserRepository($db, $crypto);
$usecase = new UpdateUserInfoService($userRepo);


$res = $usecase->execute($userInfo['userId'], $userName, $userEmail, $avatarUrl);

if (!$res) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー情報登録でエラーになりました。 userId:".$userInfo['userId'],
  ]);
  exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'message'  => "ユーザー情報を更新しました。 userId:".$userInfo['userId'],
]);
