<?php
namespace Backend\Application;

use Backend\Domain\Entities\StrokeMaster;
use Backend\Infrastructure\StrokeMasterRepository;

class GetStrokeFileId
{
  private StrokeMasterRepository $repo;

  public function __construct(StrokeMasterRepository $repo)
  {
    $this->repo = $repo;
  }

  public function execute(string $charaName): ?string
  {
    return $this->repo->getStrokeFileIdByCharaName($charaName);
  }
}