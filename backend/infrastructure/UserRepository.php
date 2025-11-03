<?php
namespace Backend\Infrastructure;

use Backend\Domain\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(User $user): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/create_user.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $user->id,
            ':name' => $user->name,
            ':email' => $user->email,
            ':picture_url' => $user->picture_url,
            ':box_user_id' => $user->box_user_id,
            ':box_access_token' => $user->box_access_token,
            ':box_refresh_token' => $user->box_refresh_token,
            ':token_expires_at' => $user->token_expires_at->format('Y-m-d H:i:s'),
            ':role_id' => $user->role_id
        ]);
    }

    public function update(User $user): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/update_user.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $user->id,
            ':name' => $user->name,
            ':email' => $user->email,
            ':picture_url' => $user->picture_url,
            ':box_user_id' => $user->box_user_id,
            ':box_access_token' => $user->box_access_token,
            ':box_refresh_token' => $user->box_refresh_token,
            ':token_expires_at' => $user->token_expires_at->format('Y-m-d H:i:s'),
            ':role_id' => $user->role_id
        ]);
    }

    public function exists(string $id): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $id]);

        return $stmt->fetch() !== false;
    }
}
