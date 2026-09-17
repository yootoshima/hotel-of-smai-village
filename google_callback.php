<?php
session_start();
include 'db.php';
$google = require __DIR__ . '/config/google_oauth.php';

function googleError(string $message): void {
    header('Location: index.php?google_error=' . rawurlencode($message));
    exit;
}

if (isset($_GET['error'])) googleError('ยกเลิกการเข้าสู่ระบบด้วย Google');
if (empty($_GET['code']) || empty($_GET['state']) || !hash_equals($_SESSION['google_oauth_state'] ?? '', $_GET['state'])) {
    googleError('ไม่สามารถยืนยันคำขอเข้าสู่ระบบ Google ได้');
}
unset($_SESSION['google_oauth_state']);

if (!function_exists('curl_init')) googleError('PHP cURL ยังไม่ได้เปิดใช้งานใน XAMPP');

$tokenRequest = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($tokenRequest, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'code' => $_GET['code'],
        'client_id' => $google['client_id'],
        'client_secret' => $google['client_secret'],
        'redirect_uri' => $google['redirect_uri'],
        'grant_type' => 'authorization_code',
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
]);
$tokenResponse = curl_exec($tokenRequest);
$tokenStatus = curl_getinfo($tokenRequest, CURLINFO_HTTP_CODE);
curl_close($tokenRequest);
$token = json_decode($tokenResponse ?: '', true);
if ($tokenStatus !== 200 || empty($token['access_token'])) googleError('Google ไม่สามารถออก access token ได้');

$userRequest = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt_array($userRequest, [CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token['access_token']], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15]);
$userResponse = curl_exec($userRequest);
$userStatus = curl_getinfo($userRequest, CURLINFO_HTTP_CODE);
curl_close($userRequest);
$googleUser = json_decode($userResponse ?: '', true);
if ($userStatus !== 200 || empty($googleUser['sub']) || empty($googleUser['email']) || empty($googleUser['email_verified'])) {
    googleError('ไม่สามารถยืนยันบัญชี Google ของคุณได้');
}

$sub = $googleUser['sub'];
$email = $googleUser['email'];
$name = trim($googleUser['name'] ?? $email);
$find = $conn->prepare('SELECT cus_id, cus_name, role FROM customers WHERE google_sub = ? OR email = ? LIMIT 1');
$find->bind_param('ss', $sub, $email);
$find->execute();
$customer = $find->get_result()->fetch_assoc();

if (!$customer) {
    $role = 'member';
    $insert = $conn->prepare('INSERT INTO customers (cus_name, password, role, google_sub, email) VALUES (?, NULL, ?, ?, ?)');
    $insert->bind_param('ssss', $name, $role, $sub, $email);
    $insert->execute();
    $customer = ['cus_id' => $conn->insert_id, 'cus_name' => $name, 'role' => $role];
} elseif (!$customer['role']) {
    googleError('ไม่พบสิทธิ์การใช้งานของบัญชีนี้');
}

$_SESSION['username'] = $customer['cus_name'];
$_SESSION['role'] = $customer['role'];
$_SESSION['cus_id'] = $customer['cus_id'];
header('Location: ' . ($customer['role'] === 'admin' ? 'admin/index.php' : 'member/index.php'));
exit;
