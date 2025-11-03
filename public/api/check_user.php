<?php
require_once __DIR__ . '/../../backend/application/ValidateTokenService.php';
$config = require __DIR__ . '/../../backend/config/env.local.php';

$jwtSecret = $config['jwt_secret'];
$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';

$validator = new Backend\Application\ValidateTokenService($jwtSecret);
$result = $validator->execute($token);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);