<?php
namespace Backend\Application;

use Backend\Infrastructure\Box\BoxAvatarUrlRepository;

class FetchUserAvatar
{
  private BoxAvatarUrlRepository $repo;
  public function __construct(BoxAvatarUrlRepository $repo)
  {
    $this->repo = $repo;
  }

  public function FetchAvaterImage(string $avatarUrl, string $accessToken): array
  {
    return $this->repo->GetAvatarImage($avatarUrl, $accessToken);
  }
}