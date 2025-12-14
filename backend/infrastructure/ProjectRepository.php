<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\Project;
use Backend\Domain\EncryptionServiceInterface;
use Backend\Domain\Service;
use Backend\Domain\Service\UuidService;
use Backend\Domain\Entities\ProjectDetails;
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

    public function existsById(string $projectId): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM projects WHERE id = :project_id LIMIT 1");
      $stmt->execute([':project_id' => $projectId]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);

      // 見つかった → id を返す
      if ($row && isset($row['id'])) {
          return $row['id'];
      }

      // 見つからなかった → null
      return null;
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
    public function getProjectDetails(string $projectId): ?ProjectDetails
    {
      $sql = file_get_contents(__DIR__ . '/../sql/get_project_details.sql');
      $stmt = $this->db->prepare($sql);
      $stmt->execute([':project_id' => $projectId]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);
      
      // 見つからなかったら null を返す
      if (!$row) {
          return null;
      }

      // 見つかったので Project オブジェクトを作って返す
      return new ProjectDetails(
          id: $row['project_id'],
          name: $row['project_name'],
          description: $row['project_description'],
          projectFolderId: $row['project_folder_id'],
          charaFolderId: $row['chara_folder_id'],
          createdAt: $row['created_at'],
          createdBy: $row['created_by'],
          updatedAt: $row['updated_at'],
          charaCount: (int)$row['chara_count'],
          participants: $row['participants']
              ? array_map('trim', explode(',', $row['participants']))
              : []
      );
    }
    public function getProjectInfo(string $projectId): ?Project
    {
      $stmt = $this->db->prepare("SELECT * FROM projects WHERE id = :project_id LIMIT 1");
      $stmt->execute([':project_id' => $projectId]);

      $row = $stmt->fetch(\PDO::FETCH_ASSOC);
      
      // 見つからなかったら null を返す
      if (!$row) {
          return null;
      }

      // 見つかったので Project オブジェクトを作って返す
      return new Project(
          id: $row['id'],
          name: $row['name'],
          description: $row['description'],
          project_folder_id: $row['folder_id'],
          chara_folder_id: $row['chara_folder_id'],
          created_by: $row['created_by']
          // created_at, updated_at を使うなら追加可能
      ); 
    }

    public function deleteByProjectId(string $projectId): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/delete_project_from_projects.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':project_id' => $projectId
        ]);
    }
    
    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollBack(): void
    {
        $this->db->rollBack();
    }


}
