<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Application\DeleteProjectService;
use Backend\Application\GetProjectDetailsService;
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
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'message'  => "入力チェックでエラーが出ました projectId:".$projectId,
  ]);
  exit;
} 

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);
if (!$userInfo['success']) {
  http_response_code(401);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "ユーザー認証でエラーが出ました userId:".$userInfo['id'],
  ]);
  exit;
}

// Project権限チェック
$userProjectsRepo = new UserProjectsRepository($db);
$getUserProjectsService = new GetUserProjectsService($userProjectsRepo);
$projectRole = $getUserProjectsService->getProjectRole($userInfo['userId'], $projectId);
// 閲覧権限がなければエラー（未参加/nullも拒否）
if ($projectRole === null || $projectRole === 'unapproved') {
  http_response_code(403);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "プロジェクトの閲覧権限がありません projectId:".$projectId." userId:".$userInfo['userId']." role:".$projectRole,
  ]);
  exit;
}

// プロジェクト詳細を取得するリポジトリ
$projectRepo = new ProjectRepository($db);
$usecase = new GetProjectDetailsService($projectRepo);

$res = $usecase->getProjectDetails($projectId);
if (!$res) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "プロジェクトの削除でエラーが出ました projectId:".$projectId,
  ]);
  exit;
}

$responseData = [
  'id' => $res->id,
  'name' => $res->name,
  'description' => $res->description,
  'project_folder_id' => $res->projectFolderId,
  'chara_folder_id' => $res->charaFolderId,
  'created_at' => $res->createdAt,
  'updated_at' => $res->updatedAt,
  'chara_count' => $res->charaCount,
  'created_by' => $res->createdBy,
  'participants' => $res->participants,
];

http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'data'  => $responseData
]);
