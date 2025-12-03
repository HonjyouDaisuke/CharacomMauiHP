<?php
namespace Backend\Application;

use Backend\Application\ValidateTokenService;

class CheckAccessToken
{
    private ValidateTokenService $validator;

    public function __construct(array $config)
    {
        // JWT validator
        $this->validator = new ValidateTokenService($config['jwt_secret']);
    }

    public function CheckAccessToken(string $token): array
    {
        // トークン検証
        return $this->validator->execute($token);
    }
}