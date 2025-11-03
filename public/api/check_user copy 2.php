<?php
require_once __DIR__ . '/../../backend/application/ValidateTokenService.php';
require_once __DIR__ . '/../../backend/infrastructure/Database.php';
require_once __DIR__ . '/../../backend/infrastructure/UserRepository.php';
require_once __DIR__ . '/../../backend/domain/EncryptionServiceInterface.php';
require_once __DIR__ . '/../../backend/infrastructure/OpenSSLEncryptionService.php';
require_once __DIR__ . '/../../backend/application/BoxTokenService.php';
$config = require __DIR__ . '/../../backend/config/env.local.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\BoxTokenService;

$jwtSecret = $config['jwt_secret'];
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
// 暗号化サービス生成
$crypto = new OpenSSLEncryptionService(
    base64_decode($config['enc_key']),
    base64_decode($config['enc_iv'])
);

// DB
$db = new Database($config);

// Repository に渡す
$repo = new UserRepository($db, $crypto);

$validator = new Backend\Application\ValidateTokenService($jwtSecret);
$result = $validator->execute($token);
$userId = $result['userId'];
$boxService = new BoxTokenService($repo, $crypto);
$tokens = $boxService->getBoxTokens($userId);
$result['boxAccessToken'] = $tokens['access_token'];
$result['boxRefreshToken'] = $tokens['refresh_token'];
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);