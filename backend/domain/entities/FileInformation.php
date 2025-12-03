<?php
namespace Backend\Domain\Entities;

class FileInformation
{
    public function __construct(
        public string $CharaName,
        public string $MaterialName = "",
        public string $TimesName = ""
    ){

    }
}

