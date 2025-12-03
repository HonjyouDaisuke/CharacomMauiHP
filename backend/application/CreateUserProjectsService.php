<?php
namespace Backend\Application;

use Backend\Infrastructure\UserProjectsRepository;

class CreateUserProjectsService
{
    private UserProjectsRepository $repo;

    public function __construct(UserProjectsRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $userId, $projectId): array
    {
        // 存在チェック
        $existingId = $this->repo->exists($userId, $projectId);

        if ($existingId !== null) {
            // UPDATE しない？
            // TODO:updateするかどうか確認
            
        } else {
            // CREATE
            $res = $this->repo->create($userId, $projectId);

            if (!$res) {
              return [
                'success' => false,
                'message' => 'Failed to create user_projects.',
              ];
            }
            return [
                'success' => true,
                'message' => 'Add User Projects with Owner.',
              ];
        }

        return [
            'success' => true,
            'message' => 'No updated.',
            'id' => "", // ← create/update で返ってきた ID をそのまま返す
        ];
    }
}
