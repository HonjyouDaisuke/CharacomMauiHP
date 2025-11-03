<?php
/**
 * migrate.php
 * 開発環境でのマイグレーション実行ツール
 * （本番では実行しないよう注意）
 */

$config = require __DIR__ . '/../backend/config/env.local.php';

// DB接続
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};port={$config['db_port']};charset=utf8mb4";
$db = new PDO($dsn, $config['db_user'], $config['db_pass'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$migrationDir = __DIR__ . '/migrations';

echo "Running migrations...\n";

// SQLを番号順に実行
$files = glob($migrationDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    echo "Applying: " . basename($file) . "\n";
    $db->exec($sql);
}

echo "✅ All migrations applied.\n";

$seedsDir = __DIR__ . '/seeds';

echo "Running seeds...\n";

// SQLを番号順に実行
$files = glob($seedsDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    echo "Applying: " . basename($file) . "\n";
    $db->exec($sql);
}

echo "✅ All seeds applied.\n";
