<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\FetchBoxFileItemService;
use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetBoxFolderItemsService;
use Backend\Infrastructure\Box\BoxFileContentRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$fileId = $data['file_id'] ?? null;
$witdth = $data['width'] ?? 128;
$height = $data['height'] ?? 128;
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

$accessToken = $userInfo['boxAccessToken'];

// ✅ UseCase（Application）
$repo = new BoxFileContentRepository();
$usecase = new FetchBoxFileItemService($repo);
// ✅ 実行
$result = $usecase->FetchBoxTumbnailItem($accessToken, $fileId, $witdth, $height);
if (!$result['success']) {
    // JSON エラーで返す
    header("Content-Type: application/json", true, $result['status'] ?: 500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch file from Box',
        'status' => $result['status'],
        'error' => $result['error']
    ]);
    exit;
}

// 画像バイナリを返す
header("Content-Type: {$result['content_type']}");
echo $result['data'];
exit;