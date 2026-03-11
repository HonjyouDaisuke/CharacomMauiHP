<?php

namespace Backend\Application;

use Backend\Infrastructure\LogRepository;
use Backend\Domain\Entities\LogData;

class InsertLogService {
  private LogRepository $repo;

  public function __construct(LogRepository $repo) {
    $this->repo = $repo;
  }

  public function execute(LogData $logData): array {
    return $this->repo->insert($logData);
  }
}
