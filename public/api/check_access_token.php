<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\CheckAccessToken;

// POSTデータ取得
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

// Config取得
$config = require __DIR__ . '/../../backend/config/env.local.php';

// AccessTokenチェック
$checkAccessToken = new CheckAccessToken($config);

$checkResult = $checkAccessToken->CheckAccessToken($token);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($checkResult);