<?php
session_start();
include '../db.php';

function customerRedirect(string $type, string $message): void {
    header('Location: customers.php?' . http_build_query([$type => $message]));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['cus_name'] ?? '');
    $phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
    $idCard = trim($_POST['id_card'] ?? '');

    if ($action === 'create' || $action === 'update') {
        if ($name === '' || $phone === '' || $idCard === '') {
            customerRedirect('error', 'กรุณากรอกชื่อ เบอร์โทรศัพท์ และเลขบัตรประชาชนให้ครบ');
        }

        if ($action === 'create') {
            $password = $_POST['password'] ?? '';
            if (strlen($password) < 6) customerRedirect('error', 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO customers (cus_name, phone, id_card, password, role) VALUES (?, ?, ?, ?, 'member')");
            $insert->bind_param('ssss', $name, $phone, $idCard, $hash);
            $insert->execute();
            customerRedirect('success', 'เพิ่มสมาชิกเรียบร้อยแล้ว');
        }

        $customerId = filter_input(INPUT_POST, 'cus_id', FILTER_VALIDATE_INT);
        if (!$customerId) customerRedirect('error', 'ไม่พบรหัสสมาชิก');
        $password = $_POST['password'] ?? '';
        if ($password !== '') {
            if (strlen($password) < 6) customerRedirect('error', 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE customers SET cus_name = ?, phone = ?, id_card = ?, password = ? WHERE cus_id = ? AND role = 'member'");
            $update->bind_param('ssssi', $name, $phone, $idCard, $hash, $customerId);
        } else {
            $update = $conn->prepare("UPDATE customers SET cus_name = ?, phone = ?, id_card = ? WHERE cus_id = ? AND role = 'member'");
            $update->bind_param('sssi', $name, $phone, $idCard, $customerId);
        }
        $update->execute();
        customerRedirect('success', 'แก้ไขข้อมูลสมาชิกเรียบร้อยแล้ว');
    }

    if ($action === 'delete') {
        $customerId = filter_input(INPUT_POST, 'cus_id', FILTER_VALIDATE_INT);
        if (!$customerId) customerRedirect('error', 'ไม่พบรหัสสมาชิก');
        $bookings = $conn->prepare('SELECT COUNT(*) AS total FROM bookings WHERE cus_id = ?');
        $bookings->bind_param('i', $customerId);
        $bookings->execute();
        if ((int) $bookings->get_result()->fetch_assoc()['total'] > 0) {
            customerRedirect('error', 'ไม่สามารถลบสมาชิกที่มีประวัติการจองได้');
        }
        $delete = $conn->prepare("DELETE FROM customers WHERE cus_id = ? AND role = 'member'");
        $delete->bind_param('i', $customerId);
        $delete->execute();
        customerRedirect('success', 'ลบสมาชิกเรียบร้อยแล้ว');
    }
}

$members = $conn->query("SELECT c.cus_id, c.cus_name, c.phone, c.id_card, COUNT(b.book_id) AS booking_count FROM customers c LEFT JOIN bookings b ON b.cus_id = c.cus_id WHERE c.role = 'member' GROUP BY c.cus_id, c.cus_name, c.phone, c.id_card ORDER BY c.cus_id DESC");
$totalMembers = $conn->query("SELECT COUNT(*) AS total FROM customers WHERE role = 'member'")->fetch_assoc()['total'];
$membersWithBooking = $conn->query("SELECT COUNT(DISTINCT cus_id) AS total FROM bookings")->fetch_assoc()['total'];
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="th">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin.css"><link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>จัดการสมาชิก | Ban Smaiy Hotel</title>
</head>
<body>
    <?php include(__DIR__ . '/../nav_admin.php'); ?>
    <main class="container mt-5 py-4 py-md-5" id="adminMain">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div><h1 class="h2 mb-1">จัดการสมาชิก</h1><p class="text-secondary mb-0">เพิ่ม แก้ไข ค้นหา และจัดการข้อมูลสมาชิกของโรงแรม</p></div>
            <button class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#memberModal" onclick="openCreateModal()"><i class="bi bi-person-plus me-1"></i>เพิ่มสมาชิก</button>
        </div>
        <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <div class="row g-3 mb-4"><div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-secondary">สมาชิกทั้งหมด</small><div class="fs-3 fw-bold"><?= (int) $totalMembers ?></div></div><i class="bi bi-people fs-2 text-success"></i></div></div></div><div class="col-md-6"><div class="card border-0 shadow-sm"><div class="card-body d-flex justify-content-between align-items-center"><div><small class="text-secondary">สมาชิกที่เคยจอง</small><div class="fs-3 fw-bold"><?= (int) $membersWithBooking ?></div></div><i class="bi bi-calendar-check fs-2 text-primary"></i></div></div></div></div>
        <section class="card border-0 shadow-sm"><div class="card-body p-3 p-md-4"><div class="input-group mb-4"><span class="input-group-text bg-white"><i class="bi bi-search"></i></span><input id="search" class="form-control border-start-0" placeholder="ค้นหาชื่อสมาชิก เบอร์โทรศัพท์ หรือเลขบัตรประชาชน"></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>#</th><th>สมาชิก</th><th>เบอร์โทรศัพท์</th><th>เลขบัตรประชาชน</th><th class="text-center">การจอง</th><th class="text-center">จัดการ</th></tr></thead><tbody id="memberRows"><?php while ($member = $members->fetch_assoc()): ?><tr><td class="fw-semibold">#<?= (int) $member['cus_id'] ?></td><td><div class="fw-semibold"><?= htmlspecialchars($member['cus_name']) ?></div><small class="text-secondary">สมาชิก</small></td><td><?= htmlspecialchars($member['phone']) ?></td><td><?= htmlspecialchars($member['id_card']) ?></td><td class="text-center"><span class="badge text-bg-primary rounded-pill"><?= (int) $member['booking_count'] ?></span></td><td class="text-center text-nowrap"><button class="btn btn-primary btn-sm" data-cus-id="<?= (int) $member['cus_id'] ?>" data-cus-name="<?= htmlspecialchars($member['cus_name'], ENT_QUOTES) ?>" data-phone="<?= htmlspecialchars($member['phone'], ENT_QUOTES) ?>" data-id-card="<?= htmlspecialchars($member['id_card'], ENT_QUOTES) ?>" onclick="openEditModal(this)"><i class="bi bi-pencil"></i> แก้ไข</button><form method="post" class="d-inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="cus_id" value="<?= (int) $member['cus_id'] ?>"><button class="btn btn-outline-danger btn-sm" onclick="return confirm('ต้องการลบสมาชิก <?= htmlspecialchars($member['cus_name'], ENT_QUOTES) ?> หรือไม่?')"><i class="bi bi-trash"></i> ลบ</button></form></td></tr><?php endwhile; ?></tbody></table><p id="emptyState" class="d-none text-center text-secondary py-4 mb-0">ไม่พบข้อมูลสมาชิก</p></div></div></section>
    </main>
    <div class="modal fade" id="memberModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 rounded-4"><form method="post" id="memberForm"><input type="hidden" name="action" id="memberAction" value="create"><input type="hidden" name="cus_id" id="customerId"><div class="modal-header border-0"><h5 class="modal-title" id="memberModalTitle">เพิ่มสมาชิก</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">ชื่อสมาชิก</label><input class="form-control" name="cus_name" id="customerName" required></div><div class="mb-3"><label class="form-label">เบอร์โทรศัพท์</label><input class="form-control" name="phone" id="customerPhone" inputmode="numeric" required></div><div class="mb-3"><label class="form-label">เลขบัตรประชาชน</label><input class="form-control" name="id_card" id="customerIdCard" maxlength="13" required></div><div class="mb-3"><label class="form-label">รหัสผ่าน <span class="text-secondary" id="passwordHint">อย่างน้อย 6 ตัวอักษร</span></label><input class="form-control" type="password" name="password" id="customerPassword"><div class="form-text" id="passwordHelp">จำเป็นสำหรับสมาชิกใหม่</div></div></div><div class="modal-footer border-0"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึกข้อมูล</button></div></form></div></div></div>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        const memberModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('memberModal'));
        const form = document.getElementById('memberForm'), password = document.getElementById('customerPassword');
        function openCreateModal() { form.reset(); customerId.value = ''; memberAction.value = 'create'; memberModalTitle.textContent = 'เพิ่มสมาชิก'; password.required = true; passwordHint.textContent = 'อย่างน้อย 6 ตัวอักษร'; passwordHelp.textContent = 'จำเป็นสำหรับสมาชิกใหม่'; }
        function openEditModal(button) { form.reset(); customerId.value = button.dataset.cusId; customerName.value = button.dataset.cusName; customerPhone.value = button.dataset.phone; customerIdCard.value = button.dataset.idCard; memberAction.value = 'update'; memberModalTitle.textContent = 'แก้ไขข้อมูลสมาชิก'; password.required = false; passwordHint.textContent = '(ไม่กรอกหากไม่ต้องการเปลี่ยน)'; passwordHelp.textContent = 'เว้นว่างเพื่อใช้รหัสผ่านเดิม'; memberModal.show(); }
        const search = document.getElementById('search'), emptyState = document.getElementById('emptyState'); search.addEventListener('input', () => { let count = 0; document.querySelectorAll('#memberRows tr').forEach(row => { const show = row.textContent.toLowerCase().includes(search.value.toLowerCase()); row.classList.toggle('d-none', !show); if (show) count++; }); emptyState.classList.toggle('d-none', count !== 0); });
    </script>
</body>
</html>
