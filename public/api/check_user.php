<?php
require_once __DIR__ . '/../../backend/helpers/GetUserInfo.php';

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
//$token = $_POST['token'] ?? '';
$userInfo = GetUserId($token);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($userInfo);