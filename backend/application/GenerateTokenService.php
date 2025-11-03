<?php
namespace Backend\Application;

use Backend\Domain\User;

class GenerateTokenService
{
    public function __construct(private string $secret, private int $expire = 3600) {}

    public function execute(User $user): array
    {
        $now = time();

        // JWT header
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        // JWT payload
        $payload = [
            'sub' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'iat' => $now,
            'exp' => $now + $this->expire
        ];

        // Base64URL encode header & payload
        $headerEncoded  = $this->base64urlEncode(json_encode($header));
        $payloadEncoded = $this->base64urlEncode(json_encode($payload));

        // Signature
        $signature = hash_hmac(
            'sha256',
            $headerEncoded . "." . $payloadEncoded,
            $this->secret,
            true
        );
        $signatureEncoded = $this->base64urlEncode($signature);

        // ✅ 正しいJWT形式 → header.payload.signature
        $accessToken = $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;

        return [
            'accessToken'  => $accessToken,
            'refreshToken' => bin2hex(random_bytes(32)),
            'expireAt'     => $now + $this->expire,
        ];
    }

    private function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
