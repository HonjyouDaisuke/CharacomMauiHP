<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../backend/domain/User.php';
require_once __DIR__ . '/../../backend/application/CreateUserService.php';
require_once __DIR__ . '/../../backend/infrastructure/Database.php';
require_once __DIR__ . '/../../backend/infrastructure/UserRepository.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Application\CreateUserService;

// 入力
$input = json_decode(file_get_contents('php://input'), true);

$email = $input['email'] ?? '';
$password = $input['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// config 読み込み
$config = require __DIR__ . '/../../backend/config/env.php';

// DI（依存注入）
$db        = new Database($config);
$repo      = new UserRepository($db);
$usecase   = new CreateUserService($repo);

// 実行
$result = $usecase->execute($email, $password);

// 出力
echo json_encode($result);
