<?php
namespace Backend\Application;

use Backend\Domain\User;
use Backend\Infrastructure\UserRepository;

class CreateUserService
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $email, string $password): array
    {
        // 1. Emailの重複チェック
        if ($this->repo->exists($email)) {
            return [
                'success' => false,
                'message' => 'Email already registered.'
            ];
        }

        // 2. Userエンティティを生成
        $user = new User($email, $password);

        // 3. DBへ保存
        $success = $this->repo->create($user);

        return [
            'success' => $success,
            'message' => $success ? 'User created successfully.' : 'Failed to create user.'
        ];
    }
}
