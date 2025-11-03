<?php
$bytes = random_bytes(32);  // 256ビット（32バイト）
$secretKey = bin2hex($bytes); // 64文字の16進数文字列
echo $secretKey;