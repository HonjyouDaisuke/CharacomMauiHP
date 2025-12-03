<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetStandardFileId;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserProjectsService;
use Backend\Infrastructure\StandardMasterRepository;
use Backend\Infrastructure\UserProjectsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$charaName = $data['chara_name'] ?? '';
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
    'success' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];

$repo = new StandardMasterRepository($db);
$usecase = new GetStandardFileId($repo);
$fileId = $usecase->execute($charaName);

if ($fileId === null)
{
  echo json_encode([
    'success' => false,
    'message'  => "file id not found",
  ]);
  exit;
}

echo json_encode([
  'success' => true,
  'file_id' => $fileId,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
