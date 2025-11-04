<?php
namespace Backend\Application;

require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Domain\Entities\User;
use Backend\Infrastructure\UserRepository;

class CreateOrUpdateUserService
{
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(User $_user): array
    {
        // 1. Emailの重複チェック
        if ($this->repo->exists($_user->id)) {
          $success = $this->repo->update($_user);
        }else{
          $success = $this->repo->create($_user);
        }

        // 3. DBへ保存
        //$success = $this->repo->create($_user);

        return [
            'success' => $success,
            'message' => $success ? 'User created successfully.' : 'Failed to create user.'
        ];
    }
}
