<?php
session_start();
$google = require __DIR__ . '/config/google_oauth.php';

if (str_starts_with($google['client_id'], 'PUT_YOUR_') || str_starts_with($google['client_secret'], 'PUT_YOUR_')) {
    exit('ยังไม่ได้ตั้งค่า Google Client ID และ Client Secret ใน config/google_oauth.php');
}

$_SESSION['google_oauth_state'] = bin2hex(random_bytes(32));
$params = [
    'client_id' => $google['client_id'],
    'redirect_uri' => $google['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'openid email profile',
    'state' => $_SESSION['google_oauth_state'],
    'prompt' => 'select_account',
];
header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit;
