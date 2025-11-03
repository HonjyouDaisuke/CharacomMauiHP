<?php
// bootstrap/config.php
$env = require __DIR__ . '/../config/env.php';

$local = __DIR__ . '/../config/env.local.php';
if (file_exists($local)) {
    $envLocal = require $local;
    $env = array_merge($env, $envLocal);
}

return $env;
