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

    public function update(CharaData $item, string $id): bool
    {
      $sql = file_get_contents(__DIR__ . '/../sql/update_chara_data.sql');
      $stmt = $this->db->prepare($sql);

      return $stmt->execute([
        ':id' => $id,
        ':material_name' => $item->material_name,
        ':chara_name' => $item->chara_name,
        ':times_name' => $item->times_name,
        ':updated_by' => $item->updated_by,
      ]);
    }
    public function isExists(CharaData $item): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM chara_data WHERE file_id=:file_id AND project_id=:project_id LIMIT 1");
      $stmt->execute([':file_id' => $item->file_id, ':project_id' => $item->project_id]);
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // 見つからなかったらすぐ null を返す（異常系）
      if ($row === false || !isset($row['id'])) {
          return null;
      }

      // 正常系：id を返す
      return $row['id'];
    }
}
