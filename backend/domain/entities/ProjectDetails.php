<?php
namespace Backend\Domain\Entities;

final class ProjectDetails
{
    /**
     * @param string   $id
     * @param string   $name
     * @param string   $description
     * @param string   $createdAt
     * @param string   $createdBy
     * @param string   $updatedAt
     * @param int      $charaCount
     * @param string[] $participants
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $projectFolderId,
        public readonly string $charaFolderId,
        public readonly string $createdAt,
        public readonly string $createdBy,
        public readonly string $updatedAt,
        public readonly int $charaCount = 0,
        public readonly array $participants = []
    ) {}
}
