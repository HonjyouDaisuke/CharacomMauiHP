<?php
namespace Backend\Infrastructure\Box;

class BoxFolderItemsRepository
{
    private string $baseUrl = "https://api.box.com/2.0";

    public function getFolderItems(string $accessToken, string $folderId): array
    {
        $url = "{$this->baseUrl}/folders/{$folderId}/items";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            return [
                'success' => false,
                'status' => $status,
                'message' => 'Failed to communicate with Box API'
            ];
        }

        return [
            'success' => true,
            'data' => json_decode($result, true)
        ];
    }
}
