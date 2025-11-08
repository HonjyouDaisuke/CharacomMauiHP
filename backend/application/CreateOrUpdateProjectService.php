<?php
namespace Backend\Application;

use Backend\Domain\Entities\Project;
use Backend\Infrastructure\ProjectRepository;

class CreateOrUpdateProjectService
{
    private ProjectRepository $repo;

    public function __construct(ProjectRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(Project $_project): array
    {
        // 存在チェック
        $id = $this->repo->exists($_project->name);
        if ($id) {
          $_project->id = $id;
          $success = $this->repo->update($_project);
        }else{
          $success = $this->repo->create($_project);
        }

        return [
            'success' => $success,
            'message' => $success ? 'User created successfully.' : 'Failed to create user.'
        ];
    }
}
