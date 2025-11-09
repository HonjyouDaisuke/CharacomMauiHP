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
        $existingId = $this->repo->exists($_project->name);

        if ($existingId !== null) {
            // UPDATE
            $_project->id = $existingId;
            $res = $this->repo->update($_project);
        } else {
            // CREATE
            $res = $this->repo->create($_project);
        }

        // 成功判定
        if ($res === "") {
            return [
                'success' => false,
                'message' => 'Failed to create or update project.',
                'id' => "",
            ];
        }

        return [
            'success' => true,
            'message' => 'Project processed successfully.',
            'id' => $res, // ← create/update で返ってきた ID をそのまま返す
        ];
    }
}
