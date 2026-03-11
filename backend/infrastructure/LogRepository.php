<?php

namespace Backend\Infrastructure;

use Backend\Domain\Entities\LogData;

use PDO;

class LogRepository {
  private PDO $db;

  public function __construct(Database $database) {
    $this->db = $database->getConnection();
  }

  public function insert(LogData $logData): array {
    try {
      $sql = file_get_contents(__DIR__ . '/../sql/insert_log.sql');
      $stmt = $this->db->prepare($sql);

      $stmt->execute([
        ':level' => $logData->level,
        ':user_id' => $logData->user_id,
        ':screen' => $logData->screen,
        ':action' => $logData->action,
        ':message' => $logData->message,
        ':data' => $logData->data ? json_encode($logData->data, JSON_UNESCAPED_UNICODE) : null,
        ':correlation_id' => $logData->correlation_id,
      ]);
      return ['success' => true];
    } catch (\Exception $e) {
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public function fetch(?string $from, ?string $to, int $limit, int $offset): ?array {
    try {
      $sql = file_get_contents(__DIR__ . '/../sql/get_logs.sql');

      $params = [];

      // 動的条件生成
      $fromCondition = "";
      $toCondition = "";

      if ($from) {
        $fromCondition = " AND created_at >= :from";
        $params[':from'] = $from . " 00:00:00";
      }

      if ($to) {
        $toCondition = " AND created_at <= :to";
        $params[':to'] = $to . " 23:59:59";
      }

      $sql = str_replace('--FROM_CONDITION--', $fromCondition, $sql);
      $sql = str_replace('--TO_CONDITION--', $toCondition, $sql);

      $stmt = $this->db->prepare($sql);

      foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
      }

      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      return null;
    }
  }

  public function getCount(?string $from, ?string $to): int {
    try {
      $sql = file_get_contents(__DIR__ . '/../sql/get_logs_count.sql');

      $params = [];

      // 動的条件生成
      $fromCondition = "";
      $toCondition = "";

      if ($from) {
        $fromCondition = " AND created_at >= :from";
        $params[':from'] = $from . " 00:00:00";
      }

      if ($to) {
        $toCondition = " AND created_at <= :to";
        $params[':to'] = $to . " 23:59:59";
      }

      $sql = str_replace('--FROM_CONDITION--', $fromCondition, $sql);
      $sql = str_replace('--TO_CONDITION--', $toCondition, $sql);

      $stmt = $this->db->prepare($sql);

      foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
      }

      $stmt->execute();

      return (int)$stmt->fetchColumn();
    } catch (\Exception $e) {
      return -1;
    }
  }
}
