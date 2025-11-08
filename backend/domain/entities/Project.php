<?php
namespace Backend\Domain\Entities;

class Project
{
    public function __construct(
        public string $id,
        public string $name = "",
        public string $description = "",
        public string $project_folder_id = "",
        public string $chara_folder_id = "",
        public string $created_by = ""
    ){

    }
}

