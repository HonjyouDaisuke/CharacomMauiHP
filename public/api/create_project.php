<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\CreateOrUpdateProjectService;
use Backend\Infrastructure\Database;
use Backend\Application\GetGlobalSettingService;
use Backend\Application\GetUserInfoService;
use Backend\Infrastructure\ProjectRepository;
use Backend\Domain\Entities\Project;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$projectFolderId = $data['project_folder_id'] ?? null;
$charaFolderId = $data['chara_folder_id'] ?? null;
$projectName = $data['name'] ?? null;
$projectDescription = $data['description'] ?? null;
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$projectFolderId || $charaFolderId || !$projectName || $projectDescription)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "invalid user info",
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
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];

$accessToken = $userInfo['boxAccessToken'];

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
    created_by: $user_id,
);

$folderList = $usecase->execute($_project);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);
