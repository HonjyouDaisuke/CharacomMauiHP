<?php

namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;
use Backend\Infrastructure\NotificationsRepository;
use Backend\Infrastructure\UserProjectsRepository;

class GetNotificationsService {
  private NotificationsRepository $repo;

  public function __construct(NotificationsRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(string $userId): ?array {
    return $this->repo->getNotificationsByUserId($userId);
  }
}
