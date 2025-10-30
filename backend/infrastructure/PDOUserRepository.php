<?php
namespace Infrastructure;

use PDO;
use Domain\Entities\User;
use Interfaces\UserRepository;

class PDOUserRepository implements UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT id, name FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new User((int)$row['id'], $row['name']);
    }

    public function save(User $user): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (id, name) VALUES (:id, :name)
                                     ON DUPLICATE KEY UPDATE name = :name');
        return $stmt->execute([
            'id' => $user->getId(),
            'name' => $user->getName()
        ]);
    }
}
