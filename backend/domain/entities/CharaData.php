<?php
namespace Backend\Domain\Entities;

class CharaData
{
    public function __construct(
        public string $id,
        public string $project_id = "",
        public string $file_id = "",
        public string $material_name = "",
        public string $chara_name = "",
        public string $times_name = "",
        public string $created_by = "",
        public bool $is_selected = false
    ){

    }
}

