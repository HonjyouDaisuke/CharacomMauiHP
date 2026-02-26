<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserRolesService;
use Backend\Infrastructure\UserRolesRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);
$crypto = new OpenSSLEncryptionService(
  base64_decode($config['enc_key']),  // decode して 32 バイトに
  base64_decode($config['enc_iv'])   // decode して 16 バイトに
);
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

// UserRolesの取得
$userRolesRepo = new UserRolesRepository($db, $crypto);
$userRolesService = new GetUserRolesService($userRolesRepo);
$roles = $userRolesService->getUserRoles();

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
