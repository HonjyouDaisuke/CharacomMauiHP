<?php

namespace Backend\Application;

use Backend\Domain\Entities\User;
use Backend\Infrastructure\ProxyLoginRepository;

class DeleteProxyLoginService {
  private ProxyLoginRepository $repo;
  public function __construct(ProxyLoginRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(string $fromUserId): array {
    $success = $this->repo->deleteByFromUserId($fromUserId);

    if (!$success) {
      return [
        'success' => false,
        'message' => 'Failed to delete proxy login record.'
      ];
    }

    return [
      'success' => $success,
      'message' => 'Proxy login record deleted successfully.',
    ];
  }
}
