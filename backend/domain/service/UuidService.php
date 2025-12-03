<?php
namespace Backend\Domain\Service;

class UuidService{
  static public function generateUuid(): string
  {
      return bin2hex(random_bytes(16)); // 32桁の hex
  }
}
