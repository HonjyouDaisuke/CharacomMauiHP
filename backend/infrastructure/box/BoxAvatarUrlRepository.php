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
      CURLOPT_HEADER => true,                // ヘッダも取得
      CURLOPT_NOBODY => false,               // ボディも取得
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$accessToken}",
      ],
      CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    // 2. 302 の場合は Location へアクセス
    if (in_array($status, [301, 302, 303, 307, 308])) {

      // Location ヘッダ抽出
      $header = substr($response, 0, $headerSize);
      if (!preg_match('/Location:\s*(.+)\r\n/i', $header, $matches)) {
        return [
          'success' => false,
          'status' => $status,
          'message' => "Redirected but Location header not found"
        ];
      }

      $redirectUrl = trim($matches[1]);

      // 3. Authorization を付けずにリダイレクト先を取得（画像本体）
      $ch2 = curl_init($redirectUrl);
      curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => false,
        CURLOPT_TIMEOUT => 15,
      ]);

      $imageData = curl_exec($ch2);
      $status2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
      curl_close($ch2);

      if ($status2 !== 200 || !$imageData) {
        return [
          'success' => false,
          'status' => $status2,
          'message' => "Failed to fetch avatar from redirect: {$redirectUrl}"
        ];
      }

      // Base64 に変換
      $base64 = base64_encode($imageData);

      return [
        'success' => true,
        'avatar_base64' => $base64,
      ];
    }

    // 3. 302 じゃなく、最初から 200 OK の場合（header と body 分離）
    if ($status === 200) {
      $body = substr($response, $headerSize);
      return [
        'success' => true,
        'avatar_base64' => base64_encode($body),
      ];
    }

    // 4. それ以外は失敗
    return [
      'success' => false,
      'status' => $status,
      'message' => "Failed to fetch avatarUrl = {$avatarUrl}"
    ];
  }
}
