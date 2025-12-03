<?php
namespace Backend\Infrastructure\Box;

class BoxFileContentRepository
{
  private string $baseUrl = "https://api.box.com/2.0";

  public function FetchFileContent(string $accessToken, string $fileId): array
  {
    $url = "{$this->baseUrl}/files/{$fileId}/content";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$accessToken}",
      ],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
    ]);

    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
      'success' => $status === 200 && $data !== false,
      'status' => $status,
      'content_type' => $info['content_type'] ?? null,
      'data' => $data,
      'error' => $curlError,
    ];
  }

  public function FetchThumbnailContent(string $accessToken, string $fileId, int $width, int $height): array
  {
    $url = "{$this->baseUrl}/files/{$fileId}/thumbnail.png?min_height={$height}&min_width={$width}";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$accessToken}",
      ],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => true,
    ]);

    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    return [
      'success' => $status === 200 && $data !== false,
      'status' => $status,
      'content_type' => $info['content_type'] ?? null,
      'data' => $data,
      'error' => $curlError,
    ];
  }
}