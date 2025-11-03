<?php
namespace Backend\Domain;

use DateTime;

class User
{
    public const ROLE_UNAPPROVED = 'unapploved';
    
    public function __construct(
        public string $id,
        public string $name = "",
        public string $email = "",
        public string $picture_url = "",
        public string $box_user_id = "",
        public string $box_access_token = "",
        public string $box_refresh_token = "",
        public ?DateTime $token_expires_at = null,
        public string $role_id = self::ROLE_UNAPPROVED
    ) {
        // DateTimeがnullなら現在日時を入れる
        $this->token_expires_at ??= new DateTime();
    }
}
