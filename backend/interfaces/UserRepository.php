<?php
namespace Interfaces;

use Domain\Entities\User;

interface UserRepository
{
    public function findById(int $id): ?User;
    public function save(User $user): bool;
}
