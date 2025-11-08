<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\Project;
use Backend\Domain\EncryptionServiceInterface;

use PDO;

class ProjectRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function create(Project $project): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/create_user.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $project->name,
            ':description' => $project->description,
            ':project_folder_id' => $project->project_folder_id,
            ':chara_folder_id' => $project->chara_folder_id,
            ':created_by' => $project->created_by,
        ]);
    }

    public function update(Project $project): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/update_user.sql');
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $project->id,
            ':name' => $project->name,
            ':description' => $project->description,
            ':project_folder_id' => $project->project_folder_id,
            ':chara_folder_id' => $project->chara_folder_id,
            ':created_by' => $project->created_by,
        ]);
    }

    public function exists(string $name): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE name=:name LIMIT 1");
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['id'];
    }
}
