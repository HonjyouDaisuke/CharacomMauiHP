<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetBoxFolderItemsService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$requestedFolderId = $data['folder_id'] ?? null;
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$globalSettingService = new GetGlobalSettingService($db);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$globalSettings = $globalSettingService->GetGlobalSetting();

//　返信結果作成
$res['success'] = true;
$res['userId'] = $userInfo['userId'];
$res['boxAccessToken'] = $userInfo['boxAccessToken'];
$res['boxRefreshToken'] = $userInfo['boxRefreshToken'];
$res['topFolderId'] = $globalSettings['topFolder'];
$res['standardFolderId'] = $globalSettings['standardFolder'];
$res['strokeFolderId'] = $globalSettings['strokeFolder'];

$accessToken = $userInfo['boxAccessToken'];

// ✅ UseCase（Application）
$usecase = new GetBoxFolderItemsService();
// ✅ 実行
$folderId = $requestedFolderId ?: $globalSettings['topFolder'];
$folderList = $usecase->execute($accessToken, $folderId);

$res['folderItems'] = $folderList['data']['entries'];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);
