<?php

namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;
use Backend\Infrastructure\UserProjectsRepository;
use Backend\Infrastructure\UserRolesRepository;

class GetUserRolesService {
  private UserRolesRepository $repo;

  public function __construct(UserRolesRepository $repo) {
    $this->repo = $repo;
  }

  public function getUserRoles(): ?array {
    return $this->repo->getUserRoles();
  }
}
