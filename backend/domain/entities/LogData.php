<?php

namespace Backend\Domain\Entities;

class LogData {
  public function __construct(
    public string $level,
    public string $user_id,
    public ?string $screen,
    public ?string $action,
    public ?string $message,
    public ?array $data,
    public ?string $correlation_id,
  ) {
  }
}
