<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\GetUserInfoService;
use Backend\Infrastructure\Database;

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

$config = require __DIR__ . '/../../backend/config/env.local.php';

// DBインスタンス
$db = new Database($config);
// UseCase
$userInfoService = new GetUserInfoService($db, $config);

$userInfo = $userInfoService->GetUserId($token);

if ($userInfo === null)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'success' => false,
    'message' => "invalid token",
  ]);
  exit;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($userInfo);