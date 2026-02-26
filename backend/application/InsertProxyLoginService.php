<?php

namespace Backend\Application;

use Backend\Domain\Entities\User;
use Backend\Infrastructure\ProxyLoginRepository;

class InsertProxyLoginService {
  private ProxyLoginRepository $repo;
  public function __construct(ProxyLoginRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(User $_fromUser, User $_toUser): array {
    $success = $this->repo->insert($_fromUser, $_toUser);

    if (!$success) {
      return [
        'success' => false,
        'message' => 'Failed to insert proxy login record.'
      ];
    }

    return [
      'success' => $success,
      'message' => 'Proxy login record inserted successfully.',
    ];
  }
}
