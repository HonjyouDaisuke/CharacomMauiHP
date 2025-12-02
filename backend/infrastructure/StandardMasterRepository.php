<?php
namespace Backend\Infrastructure;

use Backend\Domain\Entities\StandardMaster;

use PDO;

class StandardMasterRepository
{
    private PDO $db;
    
    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    public function insert(StandardMaster $item): bool
    {
        $sql = file_get_contents(__DIR__ . '/../sql/insert_standard_master.sql');
        $stmt = $this->db->prepare($sql);
   
        return $stmt->execute([
            ':chara_name' => $item->chara_name,
            ':file_id' => $item->file_id,
            ':created_by' => $item->created_by,
        ]);
    }

    public function update(StandardMaster $item, string $id): bool
    {
      $sql = file_get_contents(__DIR__ . '/../sql/update_standard_master.sql');
      $stmt = $this->db->prepare($sql);

      return $stmt->execute([
        ':id' => $id,
        ':chara_name' => $item->chara_name,
        ':file_id' => $item->file_id,
        ':updated_by' => $item->updated_by,
      ]);
    }
    public function isExists(StandardMaster $item): ?string
    {
      $stmt = $this->db->prepare("SELECT id FROM standard_master WHERE file_id=:file_id LIMIT 1");
      $stmt->execute([':file_id' => $item->file_id]);
      
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // 見つからなかったらすぐ null を返す（異常系）
      if ($row === false || !isset($row['id'])) {
          return null;
      }

      // 正常系：id を返す
      return $row['id'];
    }

    public function getStandardFileIdByCharaName(string $charaName): ?string
    {
        $sql = file_get_contents(__DIR__ . '/../sql/get_standard_file_id.sql');
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':chara_name' => $charaName]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false || !isset($row['file_id'])) {
            return null;
        }

        return $row['file_id'];
    }
}
