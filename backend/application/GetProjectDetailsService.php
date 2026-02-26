<?php

namespace Backend\Application;

use Backend\Infrastructure\ProjectRepository;
use Backend\Domain\Entities\ProjectDetails;

class GetProjectDetailsService {
  private ProjectRepository $repo;

  public function __construct(ProjectRepository $repo) {
    $this->repo = $repo;
  }

  public function getProjectDetails(string $projectId): ?ProjectDetails {
    return $this->repo->getProjectDetails($projectId);
  }
}
