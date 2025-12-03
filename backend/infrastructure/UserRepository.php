<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\User;
use Backend\Domain\EncryptionServiceInterface;
use Backend\Infrastructure\Box\BoxAvatarUrlRepository;

use PDO;

class UserRepository
{
    private PDO $db;
    private EncryptionServiceInterface $enc;

    public function __construct(Database $database, EncryptionServiceInterface $enc)
    {
        $this->db = $database->getConnection();
        $this->enc = $enc;
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
            ':box_access_token' => $this->enc->encrypt($user->box_access_token),
            ':box_refresh_token' => $this->enc->encrypt($user->box_refresh_token),
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
            ':box_access_token' => $this->enc->encrypt($user->box_access_token),
            ':box_refresh_token' => $this->enc->encrypt($user->box_refresh_token),
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

    public function getById(string $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return new User(
            id: $row['id'],
            name: $row['name'],
            email: $row['email'],
            picture_url: $row['picture_url'],
            box_user_id: $row['box_user_id'],
            box_access_token: $row['box_access_token'],
            box_refresh_token: $row['box_refresh_token'],
            token_expires_at: new \DateTime($row['token_expires_at'] ?? 'now'),
            role_id: $row['role_id'] ?? ''
        );
    }
}
