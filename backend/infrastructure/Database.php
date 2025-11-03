<?php
namespace Backend\Infrastructure;

class Database
{
    private $pdo;

    public function __construct($config)
    {
        // ポート指定を追加
        $dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset=utf8mb4";

        $this->pdo = new \PDO(
            $dsn,
            $config['db_user'],
            $config['db_pass'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
