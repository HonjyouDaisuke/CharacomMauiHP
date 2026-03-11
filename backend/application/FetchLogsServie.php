<?php

namespace Backend\Application;

use Backend\Infrastructure\LogRepository;

class FetchLogsServie {
  private LogRepository $repo;

  public function __construct(LogRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(?string $from, ?string $to, int $limit, int $offset): array {
    $logsCount = $this->repo->getCount($from, $to);
    if ($logsCount < 0) {
      return [
        'success' => false,
        'message' => "failed to count logs...",
      ];
    }

    $logs = $this->repo->fetch($from, $to, $limit, $offset);
    if ($logs === null) {
      return [
        'success' => false,
        'message' => 'fetch logs error...',
      ];
    }
    return [
      'success' => true,
      'logsCount' => $logsCount,
      'logs' => $logs,
    ];
  }
}
