<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\UpdateNotificationReadService;
use Backend\Application\UpdateSelectedCharaService;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\NotificationsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$id = $data['notification_id'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました invalid user info token:".$token." proFol:".$projectFolderId,
  ]);
  exit;
} 

// DBインスタンス
$db = new Database($config);

// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ましたinvalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}

// stroke_masterに保存
$notificationsRepo = new NotificationsRepository($db);
$updateNotificationRead = new UpdateNotificationReadService($notificationsRepo);
$res = $updateNotificationRead->execute($id);


if ($res == false) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ましたinvalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'message'  => "通知を既読に変更しました。",
]);
exit;