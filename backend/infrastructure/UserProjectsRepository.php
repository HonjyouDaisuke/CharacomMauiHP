<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\Project;
use Backend\Domain\Service\UuidService;
use PDO;

class UserProjectsRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(string $userId, string $projectId): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO user_projects (user_id, project_id, project_role_id)
            VALUES (:user_id, :project_id, 'owner')"
        );

        return $stmt->execute([
            ':user_id' => $userId,
            ':project_id' => $projectId
        ]);
    }

    public function exists(string $userId, string $projectId): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM user_projects WHERE user_id = :user_id  AND project_id = :project_id LIMIT 1");
      $stmt->execute([':user_id' => $userId, ':project_id' => $projectId]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

      // 見つかった → id を返す
      if ($row && isset($row['id'])) {
          return $row['id'];
      }

      // 見つからなかった → null
      return null;
    }
    
    public function getUserProjects(string $userId): ?array
    {
      $stmt = $this->db->prepare("SELECT project_id FROM user_projects WHERE user_id = :user_id");
      $stmt->execute([':user_id' => $userId]);

      $rows = $stmt->fetch(\PDO::FETCH_ASSOC);
      
      // 見つからなかったら null
      if (empty($rows)) {
          return null;
      }

      // 見つかったので project_id のリストを返す
      return $rows;
    }
}
