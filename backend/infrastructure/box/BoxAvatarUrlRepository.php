<?php
namespace Backend\Infrastructure\Box;

class BoxAvatarUrlRepository
{
  public function GetAvatarImage(string $avatarUrl, string $accessToken): ?array 
  {
    // 1. 最初のリクエスト（302 → Location を受け取るため HEADER を取得する）
    $ch = curl_init($avatarUrl);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => ["Authorization: Bearer {$accessToken}"],
      CURLOPT_FOLLOWLOCATION => true,   // 302リダイレクトを自動追従
      CURLOPT_TIMEOUT => 15,
    ]);

    $imageData = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200 || !$imageData) {
      return [
        'success' => false,
        'status' => $status,
        'message' => "Failed to fetch avatar from Box API",
      ];
    }

    return [
      'success' => true,
      'avatar_base64' => base64_encode($imageData),
    ];
  }
}
