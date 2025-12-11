<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Application\DeleteProjectService;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$projectId = $data['project_id'] ?? '';
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
