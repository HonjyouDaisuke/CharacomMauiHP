<?php
namespace Backend\Application;

use Backend\Infrastructure\StandardMasterRepository;

class GetStandardFileId
{
  private StandardMasterRepository $repo;

  public function __construct(StandardMasterRepository $repo)
  {
    $this->repo = $repo;
  }

  public function execute(string $charaName): ?string
  {
    return $this->repo->getStandardFileIdByCharaName($charaName);
  }
}