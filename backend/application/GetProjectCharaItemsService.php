<?php
namespace Backend\Application;

use Backend\Infrastructure\CharaDataRepository;

class GetProjectCharaItemsService
{
    private CharaDataRepository $repo;
    
    public function __construct(CharaDataRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getProjectCharaItems(string $projectId): array
    {
        return $this->repo->GetProjectCharaItems($projectId);
    }
}