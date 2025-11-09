<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\CharaData;

use PDO;

class CharaDataRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function insert(CharaData $item): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/insert_chara_data.sql');
        $stmt = $this->db->prepare($sql);
   
        return $stmt->execute([
            ':project_id' => $item->project_id,
            ':file_id' => $item->file_id,
            ':material_name' => $item->material_name,
            ':chara_name' => $item->chara_name,
            ':times_name' => $item->times_name,
            ':created_by' => $item->created_by,
        ]);
    }
}
