<?php

namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;
use Backend\Application\ProxyBoxTokenService;
use Backend\Domain\Entities\User;
use Backend\Infrastructure\ProxyLoginRepository;

class GetUserInfoService {
  private UserRepository $repo;
  private ProxyLoginRepository $proxyRepo;
  private OpenSSLEncryptionService $crypto;
  private ValidateTokenService $validator;

  public function __construct(Database $db, array $config) {
    // Crypto
    $this->crypto = new OpenSSLEncryptionService(
      base64_decode($config['enc_key']),
      base64_decode($config['enc_iv'])
    );

    // Repository
    $this->repo = new UserRepository($db, $this->crypto);
    $this->proxyRepo = new ProxyLoginRepository($db, $this->crypto);
    // JWT validator
    $this->validator = new ValidateTokenService($config['jwt_secret']);
  }

  public function GetUserId(string $token): array {
    // トークン検証
    $result = $this->validator->execute($token);
    if (!$result['success']) {
      return ['success' => false, 'message' => 'Invalid token'];
    }

    $userId = $result['userId'];
    $isProxy = $result['payload']['is_proxy'] ?? false;

    // Box token service
    $tokens = [];
    if ($isProxy) {
      $proxyBoxService = new ProxyBoxTokenService($this->proxyRepo, $this->crypto);
      $tokens = $proxyBoxService->getBoxTokens($result['payload']['from_user_id'] ?? '');
    } else {
      $boxService = new BoxTokenService($this->repo, $this->crypto);
      $tokens = $boxService->getBoxTokens($userId);
    }

    if (!$tokens) {
      return ['success' => false, 'message' => 'Failed to retrieve Box tokens'];
    }

    return [
      'success'          => true,
      'userId'           => $userId,
      'isProxy'          => $isProxy,
      'boxAccessToken'   => $tokens['access_token'] ?? null,
      'boxRefreshToken'  => $tokens['refresh_token'] ?? null,
      'payload'          => $result['payload'] ?? null,
    ];
  }

  public function GetUserInfo(string $userId): ?User {
    return  $this->repo->getById($userId);
  }

  public function GetAllUsers(): ?array {
    return  $this->repo->getAllUsers();
  }
}
