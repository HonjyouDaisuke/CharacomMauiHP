<?php

namespace Backend\Application;

use Backend\Infrastructure\ProxyLoginRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

class ProxyBoxTokenService {
  private ProxyLoginRepository $proxyRepo;
  private OpenSSLEncryptionService $crypto;

  public function __construct(ProxyLoginRepository $proxyRepo, OpenSSLEncryptionService $crypto) {
    $this->proxyRepo = $proxyRepo;
    $this->crypto = $crypto;
  }

  /**
   * @param string $userId
   * @return array|null ['access_token'=>..., 'refresh_token'=>...] or null if user not found
   */
  public function getBoxTokens(string $userId): ?array {
    $user = $this->proxyRepo->getById($userId);
    if (!$user) return null;

    return [
      'access_token'  => $this->crypto->decrypt($user['from_box_access_token']),
      'refresh_token' => $this->crypto->decrypt($user['from_box_refresh_token']),
    ];
  }
}
