<?php
namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;
use Backend\Infrastructure\ProjectRepository;
use Backend\Domain\Entities\ProjectDetails;
use Backend\Infrastructure\UserProjectsRepository;

class GetProjectDetailsService
{
    private ProjectRepository $repo;
    
    public function __construct(ProjectRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getProjectDetails(string $projectId): ?ProjectDetails
    {
        return $this->repo->getProjectDetails($projectId);
    }
}