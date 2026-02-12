<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\User;
use Backend\Domain\EncryptionServiceInterface;
use Backend\Infrastructure\Box\BoxAvatarUrlRepository;

use PDO;

class ProxyLoginRepository
{
    private PDO $db;
    private EncryptionServiceInterface $enc;

    public function __construct(Database $database, EncryptionServiceInterface $enc)
    {
        $this->db = $database->getConnection();
        $this->enc = $enc;
    }

    public function insert(User $toUser, User $fromUser): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/insert_proxy_login.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':from_user_id' => $fromUser->id,
            ':from_name' => $fromUser->name,
            ':from_email' => $fromUser->email,
            ':from_picture_url' => $fromUser->picture_url,
            ':from_box_user_id' => $fromUser->box_user_id,
            ':from_box_access_token' => $fromUser->box_access_token,
            ':from_box_refresh_token' => $fromUser->box_refresh_token,
            ':from_role_id' => $fromUser->role_id,
            ':to_user_id' => $toUser->id,
            ':to_name' => $toUser->name,
            ':to_email' => $toUser->email,
            ':to_picture_url' => $toUser->picture_url,
            ':to_box_user_id' => $toUser->box_user_id,
            ':to_box_access_token' => $toUser->box_access_token,
            ':to_box_refresh_token' => $toUser->box_refresh_token,
            ':to_role_id' => $toUser->role_id
        ]);
    }

    public function getById(string $id): ?array
    {
        $sql = file_get_contents(__DIR__ . '/../sql/get_proxy_login.sql');
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':from_user_id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return [
            'from_user_id' => $row['from_user_id'],
            'from_name' => $row['from_name'],
            'from_email' => $row['from_email'],
            'from_picture_url' => $row['from_picture_url'],
            'from_box_user_id' => $row['from_box_user_id'],
            'from_box_access_token' => $row['from_box_access_token'],
            'from_box_refresh_token' => $row['from_box_refresh_token'],
            'to_user_id' => $row['to_user_id'],
            'to_name' => $row['to_name'],
            'to_email' => $row['to_email'],
            'to_picture_url' => $row['to_picture_url'],
            'to_box_user_id' => $row['to_box_user_id'],
            'to_box_access_token' => $this->enc->decrypt($row['to_box_access_token']),
            'to_box_refresh_token' => $this->enc->decrypt($row['to_box_refresh_token']),
        ];
    }

    public function deleteByFromUserId(string $fromUserId): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/delete_proxylogin_from_user_id.sql');
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':from_user_id' => $fromUserId]);
    }
}
