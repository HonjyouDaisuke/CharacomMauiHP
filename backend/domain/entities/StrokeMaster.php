<?php
namespace Backend\Domain\Entities;

class StrokeMaster
{
    public function __construct(
        public string $id,
        public string $chara_name,
        public string $file_id = "",
        public string $created_by = "",
        public string $updated_by = "",
    ){

    }
}

