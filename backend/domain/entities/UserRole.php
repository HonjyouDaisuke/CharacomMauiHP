<?php

namespace Backend\Domain\Entities;

class UserRole {
  public function __construct(
    public string $id,
    public string $name = "",
    public string $description = "",
    public int $level = 0,
    public string $created_at = "",
    public string $updated_at = ""
  ) {
  }
}
