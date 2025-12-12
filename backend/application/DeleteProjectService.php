<?php
namespace Backend\Application;

use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\ProjectRepository;
use Backend\Infrastructure\UserProjectsRepository;

class DeleteProjectService
{
    private ProjectRepository $projectRepo;
    private UserProjectsRepository $userProjectsRepo;
    private CharaDataRepository $charaDataRepo;
    
    public function __construct(ProjectRepository $projectRepo, UserProjectsRepository $userProjectRepo, CharaDataRepository $charaDataRepo)
    {
        $this->projectRepo = $projectRepo;
        $this->userProjectsRepo = $userProjectRepo;
        $this->charaDataRepo = $charaDataRepo;
    }

    public function execute(string $projectId): bool
    {
        if (! $this->projectRepo->existsById($projectId)) {
            return false;
        }

        $this->projectRepo->beginTransaction();

        try {
          $ok =
            $this->charaDataRepo->deleteByProjectId($projectId) &&
            $this->userProjectsRepo->deleteByProjectId($projectId) &&
            $this->projectRepo->deleteByProjectId($projectId);
            
          if (! $ok) {
            $this->projectRepo->rollBack();
            return false;
          }
            $this->projectRepo->commit();
            return true;
        } catch (\Throwable $e) {
            $this->projectRepo->rollBack();
            // TODO:  エラーログ出力
            return false;
        }
    }

    
}