<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\User;
use Backend\Domain\EncryptionServiceInterface;
use Backend\Infrastructure\Box\BoxAvatarUrlRepository;

use PDO;

class UserRolesRepository
{
    private PDO $db;
    private EncryptionServiceInterface $enc;

    public function __construct(Database $database, EncryptionServiceInterface $enc)
    {
        $this->db = $database->getConnection();
        $this->enc = $enc;
    }

    public function getUserRoles(): ?array
    {
        $sql = file_get_contents(__DIR__ . '/../sql/get_user_roles.sql');
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        return $rows;
    }
}
