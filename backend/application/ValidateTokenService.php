<?php
namespace Backend\Application;

class ValidateTokenService
{
    public function __construct(private string $secret) {}

    public function execute(string $token): array
    {
        // "." で 3 分割できなければフォーマットエラー
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return ['success' => false, 'error' => 'Invalid token format (split)'];
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        // Base64URL → 通常 Base64 に変換
        $headerJson = $this->base64urlDecode($headerB64);
        $payloadJson = $this->base64urlDecode($payloadB64);

        if ($headerJson === false || $payloadJson === false) {
            return ['success' => false, 'error' => 'Invalid token format (decode)'];
        }

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);

        if (!is_array($header) || !is_array($payload)) {
            return ['success' => false, 'error' => 'Invalid token format (json)'];
        }

        // 署名を再生成
        $expected = hash_hmac('sha256', "$headerB64.$payloadB64", $this->secret, true);
        $expectedB64 = $this->base64urlEncode($expected);

        if (!hash_equals($expectedB64, $signatureB64)) {
            return ['success' => false, 'error' => 'Invalid signature'];
        }

        // 有効期限チェック
        if (isset($payload['exp']) && time() > $payload['exp']) {
            return ['success' => false, 'error' => 'Token expired'];
        }

        return [
            'success' => true,
            'userId' => $payload['sub'] ?? null,
            'payload' => $payload,
        ];
    }

    private function base64urlDecode(string $data)
    {
        $data = strtr($data, '-_', '+/');
        $padding = strlen($data) % 4;
        if ($padding > 0) {
            $data .= str_repeat('=', 4 - $padding);
        }
        return base64_decode($data);
    }

    private function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
