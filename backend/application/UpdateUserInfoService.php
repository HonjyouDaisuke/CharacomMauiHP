<?php
namespace Backend\Application;

use Backend\Domain\Entities\User;
use Backend\Infrastructure\UserRepository;

class UpdateUserInfoService
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $userId, string $userName, string $email, string $avatarUrl): array
    {
        // 1. ユーザーの存在チェック
         if (!$this->repo->exists($userId)) {
        if (!$this->repo->exists($userId)) {
          return [
            'success' => false,
            'message' => 'Failed to update user info. User not found! : '.$userId,
        ];
        }
        $success = $this->repo->updateUserInfo( $userId, $userName, $email, $avatarUrl);

        // 3. DBへ保存
        //$success = $this->repo->create($_user);

        return [
            'success' => $success,
            'message' => $success ? 'User created successfully.' : 'Failed to create user.'
        ];
    }
}
