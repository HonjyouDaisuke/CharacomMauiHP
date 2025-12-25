<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\AvatarRepository;
use Backend\Application\GetAvatarsService;
use Backend\Application\GetUserInfoService;

// POST読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

$config = require __DIR__ . '/../../backend/config/env.local.php';

// DB
$db = new Database($config);

// 認証
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'invalid user info'
    ]);
    exit;
}

// Avatar取得
$avatarRepo = new AvatarRepository(
    __DIR__ . '/../../public/avatars',
    $config['avatar_base_url']
);

$avatarService = new GetAvatarsService($avatarRepo);
$avatars = $avatarService->execute();

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'avater_dir' => __DIR__ . '/../../public/avatars',
    'base_url' => $config['avatar_base_url'],
    'user_id' => $userInfo['userId'],
    'avatars' => $avatars
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
