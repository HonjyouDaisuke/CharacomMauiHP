<?php
// helpers/GetUserInfo.php
require_once __DIR__ . '/../../backend/config/env.local.php';
require_once __DIR__ . '/../../backend/infrastructure/Database.php';
require_once __DIR__ . '/../../backend/infrastructure/UserRepository.php';
require_once __DIR__ . '/../../backend/infrastructure/OpenSSLEncryptionService.php';
require_once __DIR__ . '/../../backend/application/ValidateTokenService.php';
require_once __DIR__ . '/../../backend/application/BoxTokenService.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;

function GetUserId(string $appToken): array
{
    $config = require __DIR__ . '/../../backend/config/env.local.php';

    // 暗号化サービス
    $crypto = new OpenSSLEncryptionService(
        base64_decode($config['enc_key']),
        base64_decode($config['enc_iv'])
    );

    // DB + Repository
    $db   = new Database($config);
    $repo = new UserRepository($db, $crypto);

    // JWT検証
    $validator = new ValidateTokenService($config['jwt_secret']);
    $result = $validator->execute($appToken);

    if (empty($result['success']) || $result['success'] !== true) {
        return ['success' => false, 'message' => 'Invalid token token='.$appToken];
    }

    $userId = $result['userId'];

    // Boxトークン取得
    $boxService = new BoxTokenService($repo, $crypto);
    $tokens = $boxService->getBoxTokens($userId);

    return [
        'success'          => true,
        'userId'           => $userId,
        'boxAccessToken'   => $tokens['access_token'] ?? null,
        'boxRefreshToken'  => $tokens['refresh_token'] ?? null
    ];
}
