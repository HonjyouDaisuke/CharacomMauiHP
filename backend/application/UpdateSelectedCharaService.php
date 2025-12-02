<?php
namespace Backend\Application;

use Backend\Domain\Entities\CharaData;
use Backend\Domain\Entities\User;
use Backend\Infrastructure\CharaDataRepository;
use Backend\Infrastructure\UserRepository;

class UpdateSelectedCharaService
{
    private CharaDataRepository $repo;

    public function __construct(CharaDataRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(string $charaId, string $userId, bool $isSelected): array
    {
        $success = $this->repo->UpdateSelectedChara($charaId, $userId, $isSelected);

        return [
            'success' => $success,
            'message' => $success ? 'Update Chara select change success.' : 'Failed to chara selected change.'
        ];
    }
}
