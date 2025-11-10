<?php
namespace Backend\Application;

use Backend\Infrastructure\Box\BoxFolderItemsRepository;

class GetBoxFolderItemsService
{
    private BoxFolderItemsRepository $repo;

    public function __construct()
    {
        $this->repo = new BoxFolderItemsRepository();
    }

    public function execute(string $accessToken, string $folderId): array
    {
        return $this->repo->getAllFolderItems($accessToken, $folderId);
    }
}
