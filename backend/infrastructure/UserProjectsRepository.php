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
      $sql = file_get_contents(__DIR__ . '/../sql/get_user_projects.sql');
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

    public function getUserProjectsInfo(string $userId): ?array
    {
      $sql = file_get_contents(__DIR__ . '/../sql/get_user_projects_info.sql');
      $stmt = $this->db->prepare($sql);
      $stmt->execute([':user_id' => $userId]);

      $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $projects;
    }

    public function deleteByProjectId(string $projectId): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/delete_project_from_user_projects.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':project_id' => $projectId
        ]);
    }
    public function getUserProjectRole(string $userId, string $projectId): ?string
    {
      $sql = file_get_contents(__DIR__ . '/../sql/get_project_role.sql');
      $stmt = $this->db->prepare($sql);
      $stmt->execute([':user_id' => $userId, ':project_id' => $projectId]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

      // 見つからなかったら null を返す
      if (!$row || !isset($row['project_role_id'])) {
          return null;
      }

      // 見つかったので role_id を返す
      return $row['project_role_id'];
    }
}
