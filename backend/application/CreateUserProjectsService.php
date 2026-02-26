<?php

namespace Backend\Application;

use Backend\Infrastructure\UserProjectsRepository;

class CreateUserProjectsService {
  private UserProjectsRepository $repo;

  public function __construct(UserProjectsRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(string $userId, $projectId): array {
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
      'id' => $existingId, // ← create/update で返ってきた ID をそのまま返す
    ];
  }

  public function inviteToProject(string $toUserId, string $projectId, string $roleId): array {
    // 存在チェック
    $existingId = $this->repo->exists($toUserId, $projectId);

    if ($existingId !== null) {
      // すでにプロジェクトに参加している場合はエラーを返す
      return [
        'success' => false,
        'message' => 'User is already a member of the project.',
      ];
    }
    try {
      // CREATE
      $res = $this->repo->create($toUserId, $projectId, $roleId);
    } catch (\PDOException $e) {
      // 一意制約違反（競合による重複）
      if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1062) {
        return [
          'success' => false,
          'message' => 'User is already a member of the project.',
        ];
      }
      throw $e;
    }

    if (!$res) {
      return [
        'success' => false,
        'message' => 'Failed to invite user to project.',
      ];
    }
    return [
      'success' => true,
      'message' => 'User invited to project successfully.',
    ];
  }
}
