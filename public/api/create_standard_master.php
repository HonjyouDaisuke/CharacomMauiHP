<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\CreateOrUpdateCharaDataService;
use Backend\Application\CreateOrUpdateProjectService;
use Backend\Application\CreateOrUpdateStandardMasterService;
use Backend\Application\CreateOrUpdateStrokeMasterService;
use Backend\Application\CreateUserProjectsService;
use Backend\Application\GetBoxFolderItemsService;
use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Infrastructure\ProjectRepository;
use Backend\Domain\Entities\Project;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\StandardMasterRepository;
use Backend\Infrastructure\StrokeMasterRepository;
use Backend\Infrastructure\UserProjectsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "入力チェックでエラーが出ました invalid user info token:".$token." proFol:".$projectFolderId,
  ]);
  exit;
} 

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
    'message'  => "ユーザー認証でエラーが出ましたinvalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}

$userId = $userInfo['userId'];

$accessToken = $userInfo['boxAccessToken'];

// Chara情報取得
$boxFolderItems = new GetBoxFolderItemsService();
$globalSettings = $globalSettingService->GetGlobalSetting();

$standardFolderId = $globalSettings['standardFolder'];
$items = $boxFolderItems->execute($accessToken, $standardFolderId);

// stroke_masterに保存
$standardRepo = new StandardMasterRepository($db);
$createStandardService = new CreateOrUpdateStandardMasterService($standardRepo);
$res = $createStandardService->execute($items, $userId);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);
