<?php
session_start();
include 'db.php';

$error = '';
$success = $_GET['success'] ?? '';
$resetId = $_SESSION['password_reset_customer_id'] ?? null;
$expiresAt = $_SESSION['password_reset_expires_at'] ?? 0;
if ($resetId && $expiresAt < time()) {
    unset($_SESSION['password_reset_customer_id'], $_SESSION['password_reset_expires_at']);
    $resetId = null;
    $error = 'คำขอเปลี่ยนรหัสผ่านหมดอายุ กรุณายืนยันตัวตนใหม่';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'verify') {
        $username = trim($_POST['username'] ?? '');
        $idCard = trim($_POST['id_card'] ?? '');
        $find = $conn->prepare("SELECT cus_id FROM customers WHERE cus_name = ? AND id_card = ? AND role = 'member' AND password IS NOT NULL LIMIT 1");
        $find->bind_param('ss', $username, $idCard);
        $find->execute();
        $customer = $find->get_result()->fetch_assoc();
        if (!$customer) {
            $error = 'ไม่พบข้อมูลสมาชิกที่ตรงกัน หรือบัญชีนี้เข้าสู่ระบบด้วย Google';
        } else {
            $_SESSION['password_reset_customer_id'] = (int) $customer['cus_id'];
            $_SESSION['password_reset_expires_at'] = time() + 900;
            header('Location: forgot.php');
            exit;
        }
    }

    if ($action === 'reset' && $resetId) {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if (strlen($password) < 6) {
            $error = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        } elseif (!hash_equals($password, $confirmPassword)) {
            $error = 'ยืนยันรหัสผ่านไม่ตรงกัน';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare('UPDATE customers SET password = ? WHERE cus_id = ?');
            $update->bind_param('si', $hash, $resetId);
            $update->execute();
            unset($_SESSION['password_reset_customer_id'], $_SESSION['password_reset_expires_at']);
            header('Location: forgot.php?success=' . rawurlencode('เปลี่ยนรหัสผ่านเรียบร้อยแล้ว กรุณาเข้าสู่ระบบใหม่'));
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน | Ban Smaiy Hotel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container d-flex justify-content-center align-items-center min-vh-100 py-4">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm w-100">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4"><div class="bg-success-subtle text-success rounded-circle d-inline-flex p-3 fs-3 mb-3"><i class="bi bi-shield-lock"></i></div><h1 class="h3 mb-2">ลืมรหัสผ่าน</h1><p class="text-secondary mb-0"><?= $resetId ? 'ตั้งรหัสผ่านใหม่สำหรับบัญชีของคุณ' : 'ยืนยันตัวตนเพื่อเปลี่ยนรหัสผ่าน' ?></p></div>
                <?php if ($success): ?><div class="alert alert-success"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if (!$resetId): ?>
                    <form method="post"><input type="hidden" name="action" value="verify"><div class="mb-3"><label class="form-label">ชื่อผู้ใช้งาน</label><input class="form-control" name="username" required autofocus></div><div class="mb-3"><label class="form-label">เลขบัตรประชาชน</label><input class="form-control" name="id_card" inputmode="numeric" maxlength="13" required></div><button class="btn btn-success w-100">ยืนยันตัวตน</button></form>
                <?php else: ?>
                    <form method="post"><input type="hidden" name="action" value="reset"><div class="mb-3"><label class="form-label">รหัสผ่านใหม่</label><input class="form-control" type="password" name="password" minlength="6" required autofocus></div><div class="mb-3"><label class="form-label">ยืนยันรหัสผ่านใหม่</label><input class="form-control" type="password" name="confirm_password" minlength="6" required></div><button class="btn btn-success w-100">บันทึกรหัสผ่านใหม่</button></form>
                <?php endif; ?>
                <div class="text-center mt-4"><a href="index.php" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>กลับไปหน้าเข้าสู่ระบบ</a></div>
            </div>
        </div>
        </div>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
