<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Application\DeleteProjectService;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetUserProjectsService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$projectId = $data['project_id'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token || !$projectId)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました token:".$token." projectId:".$projectId,
  ]);
  exit;
} 

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);
if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました invalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}

// Project権限チェック
$userProjectsRepo = new UserProjectsRepository($db);
$getUserProjectsService = new GetUserProjectsService($userProjectsRepo);
$projectRole = $getUserProjectsService->getProjectRole($userInfo['userId'], $projectId);
// 管理者権限がなければエラー
if ($projectRole !== 'admin' && $projectRole !== 'owner') {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "プロジェクトの削除権限がありません projectId:".$projectId." userId:".$userInfo['userId']." role:".$projectRole,
  ]);
  exit;
}

// 削除するテーブルのリポジトリ
$projectRepo = new ProjectRepository($db);
$userProjectsRepo = new UserProjectsRepository($db);
$charaDataRepo = new CharaDataRepository($db);

$usecase = new DeleteProjectService(
    $projectRepo,
    $userProjectsRepo,
    $charaDataRepo
);

$res = $usecase->execute($projectId);

if (!$res) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "プロジェクトの削除でエラーが出ました projectId:".$projectId,
  ]);
  exit;
}
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'message'  => "プロジェクトを削除しました projectId:".$projectId,
]);
