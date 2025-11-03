<?php
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . '/../../backend/domain/entities/User.php';
require_once __DIR__ . '/../../backend/application/CreateOrUpdateUserService.php';
require_once __DIR__ . '/../../backend/application/GenerateTokenService.php';
require_once __DIR__ . '/../../backend/infrastructure/Database.php';
require_once __DIR__ . '/../../backend/infrastructure/UserRepository.php';

use Backend\Application\CreateOrUpdateUserService;
use Backend\Application\GenerateTokenService;
use Backend\Infrastructure\Database;
use Backend\Infrastructure\UserRepository;
use Backend\Domain\User;

// 開発用 CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
$config = require __DIR__ . '/../../backend/config/env.local.php';

$jwtSecret = $config['jwt_secret'];

// JSON入力
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';
$id = $input['id'] ?? '';
$name = $input['name'] ?? '';
$box_user_id = $input['box_user_id'] ?? '';
$picture_url = $input['picture_url'] ??'';
$box_access_token = $input['box_access_token'] ?? '';
$box_refresh_token = $input['box_refresh_token'] ?? '';

if (!$email || !$id) {
    echo json_encode(['success'=>false,'message'=>'Invalid input']);
    exit;
}

// DI（依存注入）
$db        = new Database($config);
$repo      = new UserRepository($db);
$usecase   = new CreateOrUpdateUserService($repo);
$generateToken = new GenerateTokenService($jwtSecret);

$_user = new User(
  id: $id,
  name: $name,
  email: $email,
  picture_url: $picture_url,
  box_access_token: $box_access_token,
  box_user_id: $box_user_id,
  box_refresh_token: $box_refresh_token,
);

$res = $usecase->execute($_user);

if (!empty($res['success']) && $res['success'] === true) {
    // JWTトークンを生成
    $token = $generateToken->execute($_user);
    // $res に token を追加
    $res['access_token'] = $token['accessToken'];
    $res['refresh_token'] = $token['refreshToken'];
    $res['expire_at'] = $token['expireAt'];
}

// レスポンスを返す
echo json_encode($res);

