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
        $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':email'    => $user->email,
            ':password' => $user->password,
        ]);
    }

    public function exists(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email=:email LIMIT 1");
        $stmt->execute([':email' => $email]);

        return $stmt->fetch() !== false;
    }
}
