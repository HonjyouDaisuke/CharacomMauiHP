<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserProjectsService;
use Backend\Infrastructure\UserProjectsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$globalSettingService = new GetGlobalSettingService($db);
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

if (!$userInfo->id || $userInfo->id == "")
{
  echo json_encode([
    'success' => false,
    'message' => "user id can't fetch userId = ".$userInfo->id,
  ]);
}

echo json_encode([
  'success' => true,
  'id' => $userInfo->id,
  'name' => $userInfo->name,
  'email' => $userInfo->email,
  'picture_url' => $userInfo->picture_url,
  'role_id' => $userInfo->role_id,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
