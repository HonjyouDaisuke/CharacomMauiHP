<?php
namespace Backend\Application;

require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;

class GetUserInfoService
{
    private UserRepository $repo;
    private OpenSSLEncryptionService $crypto;
    private ValidateTokenService $validator;

    public function __construct(Database $db, array $config)
    {
        // Crypto
        $this->crypto = new OpenSSLEncryptionService(
            base64_decode($config['enc_key']),
            base64_decode($config['enc_iv'])
        );

        // Repository
        $this->repo = new UserRepository($db, $this->crypto);

        // JWT validator
        $this->validator = new ValidateTokenService($config['jwt_secret']);
    }

    public function GetUserId(string $token): array
    {
        // トークン検証
        $result = $this->validator->execute($token);
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Invalid token'];
        }

        $userId = $result['userId'];

        // Box token service
        $boxService = new BoxTokenService($this->repo, $this->crypto);
        $tokens = $boxService->getBoxTokens($userId);

        return [
            'success'          => true,
            'userId'           => $userId,
            'boxAccessToken'   => $tokens['access_token'] ?? null,
            'boxRefreshToken'  => $tokens['refresh_token'] ?? null
        ];
    }
}