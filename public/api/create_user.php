<?php
header("Content-Type: application/json; charset=utf-8");

// CORS（開発中）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// ✅ config 読み込み
$config = require __DIR__ . '/../../backend/config/env.local.php';

// ✅ 必要ファイル読込
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Application\CreateOrUpdateUserService;
use Backend\Application\GenerateTokenService;
use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Infrastructure\OpenSSLEncryptionService;
use Backend\Domain\Entities\User;

// ✅ JSON入力読み取り
$input = json_decode(file_get_contents('php://input'), true);

$email            = $input['email'] ?? '';
$id               = $input['id'] ?? '';
$name             = $input['name'] ?? '';
$box_user_id      = $input['box_user_id'] ?? '';
$picture_url      = $input['picture_url'] ?? '';
$box_access_token = $input['box_access_token'] ?? '';
$box_refresh_token= $input['box_refresh_token'] ?? '';

if (!$email || !$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// ✅ DI（依存注入）構築
$db  = new Database($config);
$crypto = new OpenSSLEncryptionService(
    base64_decode($config['enc_key']),  // decode して 32 バイトに
    base64_decode($config['enc_iv'])   // decode して 16 バイトに
);

$repo = new UserRepository($db, $crypto);  // ← Repo が暗号化を担当
$usecase = new CreateOrUpdateUserService($repo);

$tokenService = new GenerateTokenService($config['jwt_secret']);

// ✅ エンティティ
$_user = new User(
    id: $id,
    name: $name,
    email: $email,
    picture_url: $picture_url,
    box_access_token: $box_access_token,
    box_user_id: $box_user_id,
    box_refresh_token: $box_refresh_token,
);

// ✅ usecase 実行
$res = $usecase->execute($_user);

// ✅ JWT 生成
if (!empty($res['success']) && $res['success'] === true) {

    $token = $tokenService->execute($_user);

    $res['access_token'] = $token['accessToken'];
    $res['refresh_token'] = $token['refreshToken'];
    $res['expire_at']     = $token['expireAt'];
}

// ✅ レスポンス
echo json_encode($res);
