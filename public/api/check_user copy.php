<?php
header('Content-Type: application/json; charset=utf-8');

// CORS（開発用）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// config
$config = require __DIR__ . '/../../backend/config/env.local.php';

// require
require_once __DIR__ . '/../../backend/application/ValidateTokenService.php';
require_once __DIR__ . '/../../backend/domain/entities/User.php';
require_once __DIR__ . '/../../backend/infrastructure/Database.php';
require_once __DIR__ . '/../../backend/infrastructure/UserRepository.php';
require_once __DIR__ . '/../../backend/domain/EncryptionServiceInterface.php';
require_once __DIR__ . '/../../backend/infrastructure/OpenSSLEncryptionService.php';

use Backend\Application\ValidateTokenService;
use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

// JSON入力
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

if (!$token) {
    echo json_encode(['success'=>false,'message'=>'Token missing']);
    exit;
}

// 🔑 暗号化サービス
$crypto = new OpenSSLEncryptionService(
    base64_decode($config['enc_key']),
    base64_decode($config['enc_iv'])
);

// DB
$db = new Database($config);
$repo = new UserRepository($db, $crypto);

// JWT検証
$validator = new ValidateTokenService($config['jwt_secret']);
$validationResult = $validator->execute($token);

if (!empty($validationResult['success']) && $validationResult['success'] === true) {

    $userId = $validationResult['userId'] ?? null; // JWTにuserIdが入っている想定
    if ($userId) {
        $user = $repo->getById($userId);
        if ($user) {
            // 復号して返す
            $validationResult['box_access_token']  = $crypto->decrypt($user->box_access_token);
            $validationResult['box_refresh_token'] = $crypto->decrypt($user->box_refresh_token);
        }
    }
}

echo json_encode($validationResult);
