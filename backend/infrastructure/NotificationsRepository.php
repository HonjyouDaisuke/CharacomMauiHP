<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\NotificationData;

use PDO;

class NotificationsRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function insert(NotificationData $item): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/insert_notification.sql');
        $stmt = $this->db->prepare($sql);
   
        return $stmt->execute([
            ':user_id' => $item->user_id,
            ':type_id' => $item->type_id,
            ':title' => $item->title,
            ':message' => $item->message,
            ':link_type' => $item->link_type,
            ':link_id' => $item->link_id,
            ':is_read' => $item->is_read ? 1 : 0,
            ':created_by' => $item->created_by,
        ]);
    }

    public function isReadUpdate(string $id): bool
    {
      $sql = file_get_contents(__DIR__ . '/../sql/update_notification_is_read.sql');
      $stmt = $this->db->prepare($sql);

      return $stmt->execute([
        ':id' => $id,
      ]);
    }

    public function isDeletedUpdate(string $id): bool
    {
      $sql = file_get_contents(__DIR__ . '/../sql/update_notification_is_deleted.sql');
      $stmt = $this->db->prepare($sql);

      return $stmt->execute([
        ':id' => $id,
      ]);
    }
    
    public function isExists(string $id): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM notifications WHERE id=:id LIMIT 1");
      $stmt->execute([':id' => $id]);
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // 見つからなかったらすぐ false を返す（異常系）
      if ($row === false || !isset($row['id'])) {
          return false;
      }

      // 正常系：true を返す
      return true;
    }

    public function getNotificationsByUserId(string $userId): ?array
    {
        $sql = file_get_contents(__DIR__ . '/../sql/get_notifications_by_user_id.sql');
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      
        // 見つからなかったら null
        if (empty($rows)) {
            return null;
        }

        // 見つかったので project_id のリストを返す
        return $rows;
    }
}
