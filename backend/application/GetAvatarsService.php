<?php
namespace Backend\Application;

use Backend\Infrastructure\AvatarRepository;

class GetAvatarsService
{
    private AvatarRepository $repository;

    public function __construct(AvatarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
