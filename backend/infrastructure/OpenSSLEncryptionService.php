<?php
namespace Backend\Infrastructure;

require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Domain\EncryptionServiceInterface;

class OpenSSLEncryptionService implements EncryptionServiceInterface
{
    private string $key;
    private string $iv;
    private string $cipher = "AES-256-CBC";

    public function __construct(string $key, string $iv)
    {
        if (strlen($key) !== 32) {
            throw new \Exception('Encryption key must be 32 bytes.');
        }
        if (strlen($iv) !== 16) {
            throw new \Exception('Encryption IV must be 16 bytes.');
        }
    
        $this->key = $key;
        $this->iv  = $iv;
    }

    public function encrypt(string $plaintext): string
    {
        return base64_encode(openssl_encrypt(
            $plaintext,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $this->iv
        ));
    }

    public function decrypt(string $encrypted): string
    {
        return openssl_decrypt(
            base64_decode($encrypted),
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $this->iv
        );
    }
}
