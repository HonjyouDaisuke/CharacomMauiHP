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
        public string $id,
        public string $name,
        public string $description,
        public string $projectFolderId,
        public string $charaFolderId,
        public string $createdAt,
        public string $createdBy,
        public string $updatedAt,
        public int $charaCount = 0,
        public array $participants = []
    ) {}
}
