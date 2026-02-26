<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetProjectRolesService;
use Backend\Infrastructure\ProjectRoleRepository;

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

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

// ProjectRolesの取得
$projectRolesRepo = new ProjectRoleRepository($db);
$getProjectRolesService = new GetProjectRolesService($projectRolesRepo);
$roles = $getProjectRolesService->getProjectRoles();

if ($roles === null) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'user_id' => $userInfo['userId'] ?? null,
    'message' => "There is no projects...",
  ]);
  exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'roles' => $roles,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
