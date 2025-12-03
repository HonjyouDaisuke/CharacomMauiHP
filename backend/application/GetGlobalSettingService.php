<?php
namespace Backend\Application;

use Backend\Infrastructure\Database;
use Backend\Infrastructure\GlobalSettingRepository;

class GetGlobalSettingService
{
    private GlobalSettingRepository $repo;

    public function __construct(Database $db)
    {
        $this->repo = new GlobalSettingRepository($db);
    }

    public function GetGlobalSetting(): array
    {
        $res = $this->repo->getGlobalSetting();

        if (!$res) {
            return [
                'success' => false,
                'message' => 'Global settings not found',
            ];
        }

        return [
            'success'        => true,
            'topFolder'      => $res['top_folder_id'] ?? null,
            'standardFolder' => $res['standard_folder_id'] ?? null,
            'strokeFolder'   => $res['stroke_folder_id'] ?? null,
        ];
    }
}