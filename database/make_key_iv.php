<?php
// generate_key_iv.php
// Just echo key & iv (base64 encoded)

$key = base64_encode(random_bytes(32)); // 32 bytes → AES-256
$iv  = base64_encode(random_bytes(16)); // 16 bytes → AES block size

echo "KEY_BASE64={$key}\n";
echo "IV_BASE64={$iv}\n";
