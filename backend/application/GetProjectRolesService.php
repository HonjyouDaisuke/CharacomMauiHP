<?php

namespace Backend\Application;

use Backend\Infrastructure\ProjectRoleRepository;
use Backend\Domain\Entities\ProjectDetails;

class GetProjectRolesService {
  private ProjectRoleRepository $repo;

  public function __construct(ProjectRoleRepository $repo) {
    $this->repo = $repo;
  }

  public function getProjectRoles(): ?array {
    return $this->repo->getProjectRoles();
  }
}
