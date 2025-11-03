<?php
namespace Backend\Domain;

interface EncryptionServiceInterface
{
    public function encrypt(string $plaintext): string;
    public function decrypt(string $encrypted): string;
}
