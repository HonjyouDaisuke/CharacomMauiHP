<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\Project;
use Backend\Domain\Service\UuidService;
use PDO;

class ProjectRoleRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function getProjectRoles(): ?array
    {
      $sql = file_get_contents(__DIR__ . '/../sql/get_project_roles.sql');
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      
      $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      
      // 見つからなかったら null
      if (empty($rows)) {
          return null;
      }

      // 見つかったので project_id のリストを返す
      return $rows;
    }
}
