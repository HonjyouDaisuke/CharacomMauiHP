<?php
namespace Backend\Domain\Entities;

class NotificationData
{
    public function __construct(
        public string $id = "",
        public string $user_id = "",
        public string $type_id = "",
        public string $title = "",
        public string $message = "",
        public string $link_type = "",
        public string $link_id = "",
        public bool $is_read = false,
        public string $created_by = ""
    ){

    }
}

