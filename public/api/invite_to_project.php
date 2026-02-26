<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\GetProjectRolesService;
use Backend\Application\CreateUserProjectsService;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\NotificationsRepository;
use Backend\Application\InsertNotificationService;
use Backend\Domain\Entities\NotificationData;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$projectId = $data['project_id'] ?? '';
$toUserId = $data['to_user_id'] ?? '';
$toRoleId = $data['to_role_id'] ?? '';
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success'] || !$projectId || !$toUserId || !$toRoleId) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

// UserProjectsに挿入
$userProjectRepo = new UserProjectsRepository($db);
$userProjectsService = new CreateUserProjectsService($userProjectRepo);
$success = $userProjectsService->inviteToProject($toUserId, $projectId, $toRoleId);

if (!$success['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => $success['message'] ?? " Failed to invite user to project.",
  ]);
  exit;
}

// ユーザー名の取得
$getUserInfoService = new GetUserInfoService($db, $config);
$fromUserInfo = $getUserInfoService->GetUserInfo($userInfo['userId']);
$fromUserName = $fromUserInfo?->name ?? "Unknown User";

$toUserInfo = $getUserInfoService->GetUserInfo($toUserId);
$toUserName = $toUserInfo?->name ?? "Unknown User";

// プロジェクト名の取得
$projectRepo = new ProjectRepository($db);
$project = $projectRepo->getProjectDetails($projectId);
$projectName = $project?->name ?? "Unknown Project";

// Notificationsの挿入(招待相手)
$notificationRepo = new NotificationsRepository($db);
$insertNotificationService = new InsertNotificationService($notificationRepo);
$notificationData = new NotificationData(
  user_id: $toUserId,
  type_id: 'project_invite',
  title: "プロジェクトに招待されました。",
  message: $fromUserName . "さんがプロジェクトに招待しました。プロジェクト名: $projectName 権限: $toRoleId",
  is_read: false,
  created_by: $userInfo['userId']
);
$insertNotificationResult = $insertNotificationService->execute($notificationData);
// Notificationsの挿入エラーチェック
if (!$insertNotificationResult['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'message' => $insertNotificationResult['message']]);
  exit;
}

// Notificationsの挿入(自分用)
$notificationData = new NotificationData(
  user_id: $fromUserInfo->id ?? $userInfo['user_id'],
  type_id: 'system',
  title: "プロジェクトに招待しました。",
  message: $toUserName . "さんをプロジェクトに招待しました。プロジェクト名: $projectName 権限: $toRoleId",
  is_read: false,
  created_by: $userInfo['userId']
);
$insertNotificationResult = $insertNotificationService->execute($notificationData);

// Notificationsの挿入エラーチェック
if (!$insertNotificationResult['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => $insertNotificationResult['message'] ?? " Failed to insert notification.",
  ]);
  exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'message' => $success['message'] ?? " User invited to project successfully.$toRoleId",
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
