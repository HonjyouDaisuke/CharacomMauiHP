<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\CreateOrUpdateCharaDataService;
use Backend\Application\CreateOrUpdateProjectService;
use Backend\Application\CreateUserProjectsService;
use Backend\Application\GetBoxFolderItemsService;
use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Infrastructure\ProjectRepository;
use Backend\Domain\Entities\Project;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$projectFolderId = $data['project_folder_id'] ?? null;
$charaFolderId = $data['chara_folder_id'] ?? null;
$projectName = $data['name'] ?? null;
$projectDescription = $data['description'] ?? null;
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$projectFolderId || !$charaFolderId || !$projectName || !$projectDescription)
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
$globalSettingService = new GetGlobalSettingService($db);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ましたinvalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}

$userId = $userInfo['userId'];

$accessToken = $userInfo['boxAccessToken'];
// Chara情報取得
$boxFolderItems = new GetBoxFolderItemsService();
// ✅ UseCase（Application）
$repo = new ProjectRepository($db);
$usecase = new CreateOrUpdateProjectService($repo);
// ✅ エンティティ
$_project = new Project(
    id: "",
    name: $projectName,
    description: $projectDescription,
    project_folder_id: $projectFolderId,
    chara_folder_id: $charaFolderId,
    created_by: $userId,
);

$res = $usecase->execute($_project);
if(!$res['success'])
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($res);
  exit;
}
$projectId = $res['id'];

// Chara情報取得
$boxFolderItems = new GetBoxFolderItemsService();
$items = $boxFolderItems->execute($accessToken, $charaFolderId);

$charaDataRepo = new CharaDataRepository($db);
$insertCharaDataUseCase = new CreateOrUpdateCharaDataService($charaDataRepo);
$res = $insertCharaDataUseCase->execute($items, $projectId, $userId);

// User Projects にオーナーとして追加
$userProjectsRepo = new UserProjectsRepository($db);
$createUserProjectsUseCase = new CreateUserProjectsService($userProjectsRepo);
$res = $createUserProjectsUseCase->execute($userId, $projectId);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);
