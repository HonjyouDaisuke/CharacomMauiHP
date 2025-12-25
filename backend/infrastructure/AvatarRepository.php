<?php
namespace Backend\Infrastructure;

class AvatarRepository
{
    private string $avatarDir;
    private string $baseUrl;

    public function __construct(string $avatarDir, string $baseUrl)
    {
        $this->avatarDir = rtrim($avatarDir, '/');
        $this->baseUrl   = rtrim($baseUrl, '/');
    }

    public function getAll(): array
    {
        if (!is_dir($this->avatarDir)) {
            return [];
        }

        $avatars = [];

        foreach (scandir($this->avatarDir) as $file) {
            if ($file === '.' || $file === '..') continue;

            $path = $this->avatarDir . '/' . $file;

            if (is_file($path) && preg_match('/\.(png|jpg|jpeg|webp)$/i', $file)) {
                $avatars[] = [
                    'name' => $file,
                    'url'  => $this->baseUrl . '/' . $file
                ];
            }
        }

        return $avatars;
    }
}
