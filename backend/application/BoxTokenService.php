<?php
namespace Backend\Application;

require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;

class BoxTokenService
{
    private UserRepository $userRepo;
    private OpenSSLEncryptionService $crypto;

    public function __construct(UserRepository $userRepo, OpenSSLEncryptionService $crypto)
    {
        $this->userRepo = $userRepo;
        $this->crypto = $crypto;
    }

    /**
     * @param string $userId
     * @return array|null ['access_token'=>..., 'refresh_token'=>...] or null if user not found
     */
    public function getBoxTokens(string $userId): ?array
    {
        $user = $this->userRepo->getById($userId);
        if (!$user) return null;

        return [
            'access_token'  => $this->crypto->decrypt($user->box_access_token),
            'refresh_token' => $this->crypto->decrypt($user->box_refresh_token),
        ];
    }
}
