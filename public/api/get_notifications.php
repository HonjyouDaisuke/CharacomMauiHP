<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetNotificationsService;
use Backend\Infrastructure\NotificationsRepository;

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

// Notificationを取得
$repo = new NotificationsRepository($db);
$getNotificationsService = new GetNotificationsService($repo);
$notifications = $getNotificationsService->execute($userInfo['userId']);

if (!$notifications)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => $success['message'] ?? " Failed to invite user to project.",
  ]);
  exit;
}

foreach ($notifications as &$item) {
    $item['is_read'] = $item['is_read'] ? true : false;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'notifications' => $notifications,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
