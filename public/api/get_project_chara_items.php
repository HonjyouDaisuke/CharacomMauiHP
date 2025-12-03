<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetProjectCharaItemsService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserProjectsService;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$project_id = $data['project_id'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];

// Projectsの取得
$charaDataRepo = new CharaDataRepository($db);
$projectCharaItemsService = new GetProjectCharaItemsService($charaDataRepo);

$charaDataItems = $projectCharaItemsService->getProjectCharaItems($project_id);

if ($charaDataItems === null)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => "There is no Items... projectId={$projectId}",
  ]);
  exit;
}

foreach ($charaDataItems as &$item) {
    $item['is_selected'] = $item['is_selected'] ? true : false;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'items' => $charaDataItems,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
