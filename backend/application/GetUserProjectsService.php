<?php
namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Application\ValidateTokenService;
use Backend\Application\BoxTokenService;
use Backend\Infrastructure\UserProjectsRepository;

class GetUserProjectsService
{
    private UserProjectsRepository $repo;
    
    public function __construct(UserProjectsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getUserProjects(string $userId): array
    {
        return $this->repo->getUserProjectsInfo($userId);
    }
}