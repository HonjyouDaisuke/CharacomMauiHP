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

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];

// Projectsの取得
$userProjectsRepo = new UserProjectsRepository($db);
$userProjectsService = new GetUserProjectsService($userProjectsRepo);
$projects = $userProjectsService->getUserProjects($userId);

if ($projects === null)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'user_id' => $userId,
    'message' => "There is no projects...",
  ]);
  exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'user_id' => $userId,
  'projects' => $projects,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
