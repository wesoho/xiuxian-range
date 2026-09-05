<?php
// 【修复/安全】综合密码学实践：AES-GCM + Argon2id + 安全随机数

// 1. AES-GCM（而非 ECB）
$key = random_bytes(32);
$iv = random_bytes(12);  // GCM 用 12 字节 IV
$tag = '';
$ciphertext = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

// 2. HMAC（而非 H(secret||data)）
$signature = hash_hmac('sha256', $data, $secret);

// 3. 强 JWT
// 使用 lib 库（firebase/php-jwt），指定算法，禁止 alg=none