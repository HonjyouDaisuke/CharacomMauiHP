<?php
require_once __DIR__ . '/../../backend/vendor/autoload.php';

use Backend\Infrastructure\Database;
use Backend\Application\GetUserInfoService;
use Backend\Application\UpdateSelectedCharaService;
use Backend\Infrastructure\CharaDataRepository;

// POST情報読み取り
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$id = $data['chara_id'] ?? '';
$isSelected = $data['is_selected'] ?? false;
$config = require __DIR__ . '/../../backend/config/env.local.php';

// 入力チェック
if (!$token)
{
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "入力チェックでエラーが出ました invalid user info token:".$token." proFol:".$projectFolderId,
  ]);
  exit;
} 

// DBインスタンス
$db = new Database($config);

// UseCase
$userInfoService = new GetUserInfoService($db, $config);
$userInfo = $userInfoService->GetUserId($token);

if (!$userInfo['success']) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'succeess' => false,
    'message'  => "ユーザー認証でエラーが出ましたinvalid user info\ntoken=".$token."\n userId:".$userInfo['id'],
  ]);
  exit;
}

$userId = $userInfo['userId'];

// stroke_masterに保存
$charaDataRepo = new CharaDataRepository($db);
$updateCharaSelectedService = new UpdateSelectedCharaService($charaDataRepo);
$res = $updateCharaSelectedService->execute($id, $userId, $isSelected);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($res);
