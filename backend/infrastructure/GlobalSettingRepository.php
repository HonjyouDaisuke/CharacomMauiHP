<?php
namespace Backend\Infrastructure;

use PDO;

class GlobalSettingRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->getConnection();
    }

    /**
     * top_folder_id を取得
     */
    public function getGlobalSetting(): ?array
    {
        $sql = file_get_contents(__DIR__ . '/../sql/get_global_setting.sql');
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $row;
    }
}
