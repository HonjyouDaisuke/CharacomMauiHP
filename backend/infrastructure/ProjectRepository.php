<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\Project;
use Backend\Domain\EncryptionServiceInterface;
use Backend\Domain\Service;
use Backend\Domain\Service\UuidService;
use PDO;

class ProjectRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(Project $project): string
    {
        $sql = file_get_contents(__DIR__ . '/../sql/create_project.sql');
        $stmt = $this->db->prepare($sql);
        $newId = UuidService::generateUuid();

        $res = $stmt->execute([
            ':id' => $newId,
            ':name' => $project->name,
            ':description' => $project->description,
            ':folder_id' => $project->project_folder_id,
            ':chara_folder_id' => $project->chara_folder_id,
            ':created_by' => $project->created_by,
        ]);

        return $res ? $newId : "";
    }

    public function update(Project $project): string
    {
        $sql = file_get_contents(__DIR__ . '/../sql/update_project.sql');
        $stmt = $this->db->prepare($sql);

        $res = $stmt->execute([
            ':id' => $project->id,
            ':name' => $project->name,
            ':description' => $project->description,
            ':folder_id' => $project->project_folder_id,
            ':chara_folder_id' => $project->chara_folder_id,
            ':created_by' => $project->created_by,
        ]);

        return $res ? $project->id : "";
    }

    public function exists(string $name): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM projects WHERE name = :name LIMIT 1");
      $stmt->execute([':name' => $name]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

      // 見つかった → id を返す
      if ($row && isset($row['id'])) {
          return $row['id'];
      }

      // 見つからなかった → null
      return null;
    }

}
