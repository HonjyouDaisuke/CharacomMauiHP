<?php
namespace Backend\Application;

use Backend\Infrastructure\Box\BoxAvatarUrlRepository;
use Backend\Infrastructure\Box\BoxFileContentRepository;

class FetchBoxFileItemService
{
  private BoxFileContentRepository $repo;
  
  public function __construct(BoxFileContentRepository $repo)
  {
    $this->repo = $repo;
  }

  public function FetchBoxFileItem(string $accessToken, string $fileId): array
  {
    return $this->repo->FetchFileContent( $accessToken, $fileId);
  }

  public function FetchBoxTumbnailItem(string $accessToken, string $fileId, int $width, int $height): array
  {
    return $this->repo->FetchThumbnailContent( $accessToken, $fileId, $width, $height);
  }
}
