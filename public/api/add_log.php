<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\InsertLogService;
use Backend\Domain\Entities\LogData;
use Backend\Infrastructure\LogRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);

$token = $data['token'] ?? '';
$logInput = $data['log_data'] ?? null;

if (!$token || !$logInput) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => false, 'message' => 'Invalid request']);
  exit;
}
$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);

// User認証 & User情報取得
// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message'  => "invalid user info",
  ]);
  exit;
}

$userId = $userInfo['userId'];

// ログデータの作成
$logData = new LogData(
  level: $logInput['level'] ?? 'Information',
  user_id: $userId, // token由来
  screen: $logInput['screen'] ?? null,
  action: $logInput['action'] ?? null,
  message: $logInput['message'] ?? null,
  data: $logInput['data'] ?? null,
  correlation_id: $logInput['correlation_id'] ?? null
);

$logRepo = new LogRepository($db);
$insertService = new InsertLogService($logRepo);
$success = $insertService->execute($logData);

if (!$success['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => $success['message'] ?? " Failed to insert log data.",
  ]);
  exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'success' => true,
  'message' => "insert log data.",
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
