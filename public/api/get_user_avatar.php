<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\FetchUserAvatar;
use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Infrastructure\Box\BoxAvatarUrlRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
// UseCase

$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

header('Content-Type: application/json; charset=utf-8');
if (!$userInfo['success']) {
  echo json_encode([
    'succeess' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];
$userInfo = $userInfoService->GetUserInfo($userId);

if ($userInfo === null)
{
  echo json_encode([
    'succeess' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

if ($userInfo->picture_url === "")
{
  echo json_encode([
    'success' => false,
    'message' => "user id can't fetch picture_url userId = {$userId}",
  ]);
}

$avatarRepo = new BoxAvatarUrlRepository();
$avatarUseCase = new FetchUserAvatar($avatarRepo);
$res = $avatarUseCase->FetchAvaterImage($userInfo->picture_url, $userInfo->box_access_token);

echo json_encode($res);
