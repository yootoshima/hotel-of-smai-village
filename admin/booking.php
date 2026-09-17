<?php
session_start();
include '../db.php';

function redirectWithMessage(string $type, string $message): void {
    header('Location: booking.php?' . http_build_query([$type => $message]));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $customerId = filter_input(INPUT_POST, 'cus_id', FILTER_VALIDATE_INT);
        $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
        $checkIn = $_POST['check_in'] ?? '';
        $checkOut = $_POST['check_out'] ?? '';

        if (!$customerId || !$roomId || !$checkIn || !$checkOut || $checkOut <= $checkIn) {
            redirectWithMessage('error', 'กรุณากรอกข้อมูลการจองให้ครบ และเลือกวันออกหลังวันเข้าพัก');
        }

        $room = $conn->prepare('SELECT price FROM rooms WHERE room_id = ? AND status NOT IN (\'ปิดใช้งาน\', \'ซ่อมบำรุง\')');
        $room->bind_param('i', $roomId);
        $room->execute();
        $roomData = $room->get_result()->fetch_assoc();
        if (!$roomData) {
            redirectWithMessage('error', 'ไม่พบห้องพัก หรือห้องพักไม่พร้อมให้จอง');
        }

        $overlap = $conn->prepare("SELECT book_id FROM bookings WHERE room_id = ? AND status IN ('รอตรวจสอบ', 'จองแล้ว', 'เข้าพัก') AND check_in < ? AND check_out > ?");
        $overlap->bind_param('iss', $roomId, $checkOut, $checkIn);
        $overlap->execute();
        if ($overlap->get_result()->num_rows > 0) {
            redirectWithMessage('error', 'ห้องพักนี้มีรายการจองในช่วงวันที่เลือกแล้ว');
        }

        $nights = (new DateTime($checkIn))->diff(new DateTime($checkOut))->days;
        $totalPrice = (float) $roomData['price'] * $nights;
        $booking = $conn->prepare("INSERT INTO bookings (cus_id, room_id, check_in, check_out, total_price, status) VALUES (?, ?, ?, ?, ?, 'รอตรวจสอบ')");
        $booking->bind_param('iissd', $customerId, $roomId, $checkIn, $checkOut, $totalPrice);
        $booking->execute();
        redirectWithMessage('success', 'เพิ่มคำขอจองเรียบร้อยแล้ว');
    }

    $bookingId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    if (!$bookingId) {
        redirectWithMessage('error', 'ไม่พบรายการจองที่ต้องการจัดการ');
    }

    if ($action === 'approve') {
        $booking = $conn->prepare("SELECT room_id FROM bookings WHERE book_id = ? AND status = 'รอตรวจสอบ'");
        $booking->bind_param('i', $bookingId);
        $booking->execute();
        $bookingData = $booking->get_result()->fetch_assoc();
        if (!$bookingData) {
            redirectWithMessage('error', 'รายการนี้ไม่อยู่ในสถานะรอตรวจสอบ');
        }

        $conn->begin_transaction();
        try {
            $updateBooking = $conn->prepare("UPDATE bookings SET status = 'จองแล้ว' WHERE book_id = ?");
            $updateBooking->bind_param('i', $bookingId);
            $updateBooking->execute();
            $updateRoom = $conn->prepare("UPDATE rooms SET status = 'ไม่ว่าง' WHERE room_id = ?");
            $updateRoom->bind_param('i', $bookingData['room_id']);
            $updateRoom->execute();
            $conn->commit();
        } catch (Throwable $error) {
            $conn->rollback();
            redirectWithMessage('error', 'ไม่สามารถยืนยันการจองได้');
        }
        redirectWithMessage('success', 'ยืนยันการจองเรียบร้อยแล้ว');
    }

    if ($action === 'cancel') {
        $booking = $conn->prepare("SELECT room_id FROM bookings WHERE book_id = ? AND status IN ('รอตรวจสอบ', 'จองแล้ว')");
        $booking->bind_param('i', $bookingId);
        $booking->execute();
        $bookingData = $booking->get_result()->fetch_assoc();
        if (!$bookingData) {
            redirectWithMessage('error', 'รายการนี้ไม่สามารถยกเลิกได้');
        }

        $conn->begin_transaction();
        try {
            $updateBooking = $conn->prepare("UPDATE bookings SET status = 'ยกเลิก' WHERE book_id = ?");
            $updateBooking->bind_param('i', $bookingId);
            $updateBooking->execute();
            $active = $conn->prepare("SELECT COUNT(*) AS total FROM bookings WHERE room_id = ? AND status IN ('รอตรวจสอบ', 'จองแล้ว', 'เข้าพัก')");
            $active->bind_param('i', $bookingData['room_id']);
            $active->execute();
            if ((int) $active->get_result()->fetch_assoc()['total'] === 0) {
                $updateRoom = $conn->prepare("UPDATE rooms SET status = 'ว่าง' WHERE room_id = ?");
                $updateRoom->bind_param('i', $bookingData['room_id']);
                $updateRoom->execute();
            }
            $conn->commit();
        } catch (Throwable $error) {
            $conn->rollback();
            redirectWithMessage('error', 'ไม่สามารถยกเลิกการจองได้');
        }
        redirectWithMessage('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
    }
}

$customers = $conn->query("SELECT cus_id, cus_name FROM customers WHERE role = 'member' ORDER BY cus_name");
$rooms = $conn->query("SELECT room_id, room_number, room_type, price FROM rooms WHERE status NOT IN ('ปิดใช้งาน', 'ซ่อมบำรุง') ORDER BY room_number");
$bookings = $conn->query("SELECT b.book_id, b.check_in, b.check_out, b.total_price, b.status, c.cus_name, r.room_number, r.room_type FROM bookings b LEFT JOIN customers c ON c.cus_id = b.cus_id LEFT JOIN rooms r ON r.room_id = b.room_id ORDER BY b.book_id DESC");
$counts = ['รอตรวจสอบ' => 0, 'จองแล้ว' => 0, 'เข้าพัก' => 0, 'ยกเลิก' => 0];
$countResult = $conn->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status');
while ($count = $countResult->fetch_assoc()) {
    if (array_key_exists($count['status'], $counts)) $counts[$count['status']] = (int) $count['total'];
}
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>จัดการการจอง | Ban Smaiy Hotel</title>
</head>

<body>
    <?php include(__DIR__ . '/../nav_admin.php'); ?>
    <main class="container mt-5 py-4 py-md-5" id="adminMain">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">จัดการการจอง</h1>
                <p class="text-secondary mb-0">เพิ่ม ตรวจสอบ ยืนยัน และยกเลิกรายการจองห้องพัก</p>
            </div>
            <button class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#bookingModal"><i
                    class="bi bi-calendar-plus me-1"></i>เพิ่มการจอง</button>
        </div>

        <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><i
                class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?><button class="btn-close"
                data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><i
                class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?><button class="btn-close"
                data-bs-dismiss="alert"></button></div><?php endif; ?>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div><small class="text-secondary">รอตรวจสอบ</small>
                            <div class="fs-3 fw-bold"><?= $counts['รอตรวจสอบ'] ?></div>
                        </div><i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div><small class="text-secondary">ยืนยันแล้ว</small>
                            <div class="fs-3 fw-bold"><?= $counts['จองแล้ว'] ?></div>
                        </div><i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div><small class="text-secondary">เข้าพัก</small>
                            <div class="fs-3 fw-bold"><?= $counts['เข้าพัก'] ?></div>
                        </div><i class="bi bi-door-open fs-3 text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div><small class="text-secondary">ยกเลิก</small>
                            <div class="fs-3 fw-bold"><?= $counts['ยกเลิก'] ?></div>
                        </div><i class="bi bi-x-circle fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>

        <section class="card border-0 shadow-sm">
            <div class="card-body p-3 p-md-4">
                <div class="row g-2 mb-4">
                    <div class="col-md-8">
                        <div class="input-group"><span class="input-group-text bg-white"><i
                                    class="bi bi-search"></i></span><input id="search"
                                class="form-control border-start-0"
                                placeholder="ค้นหาชื่อลูกค้า เลขการจอง หรือหมายเลขห้อง"></div>
                    </div>
                    <div class="col-md-4"><select id="statusFilter" class="form-select">
                            <option value="">ทุกสถานะ</option>
                            <option>รอตรวจสอบ</option>
                            <option>จองแล้ว</option>
                            <option>เข้าพัก</option>
                            <option>เสร็จสิ้น</option>
                            <option>ยกเลิก</option>
                        </select></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>ผู้จอง</th>
                                <th>ห้อง</th>
                                <th>วันเข้าพัก</th>
                                <th>วันออก</th>
                                <th class="text-end">ยอดรวม</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="bookingRows">
                            <?php while ($booking = $bookings->fetch_assoc()):
                $badge = match ($booking['status']) { 'รอตรวจสอบ' => 'text-bg-warning', 'จองแล้ว' => 'text-bg-primary', 'เข้าพัก' => 'text-bg-success', 'ยกเลิก' => 'text-bg-danger', default => 'text-bg-secondary' }; ?>
                            <tr data-status="<?= htmlspecialchars($booking['status']) ?>">
                                <td class="fw-semibold">#<?= (int) $booking['book_id'] ?></td>
                                <td><?= htmlspecialchars($booking['cus_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['room_number'] ?? '-') ?><br><small
                                        class="text-secondary"><?= htmlspecialchars($booking['room_type'] ?? '') ?></small>
                                </td>
                                <td><?= $booking['check_in'] ? date('d/m/Y', strtotime($booking['check_in'])) : '-' ?>
                                </td>
                                <td><?= $booking['check_out'] ? date('d/m/Y', strtotime($booking['check_out'])) : '-' ?>
                                </td>
                                <td class="text-end">฿<?= number_format((float) $booking['total_price'], 2) ?></td>
                                <td class="text-center"><span
                                        class="badge <?= $badge ?> rounded-pill px-3 py-2"><?= htmlspecialchars($booking['status']) ?></span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <?php if ($booking['status'] === 'รอตรวจสอบ'): ?><form class="d-inline"
                                        method="post"><input type="hidden" name="book_id"
                                            value="<?= (int) $booking['book_id'] ?>"><button name="action"
                                            value="approve" class="btn btn-success btn-sm"
                                            onclick="return confirm('ยืนยันรายการจองนี้หรือไม่?')"><i
                                                class="bi bi-check-lg"></i> ยืนยัน</button></form>
                                    <form class="d-inline" method="post"><input type="hidden" name="book_id"
                                            value="<?= (int) $booking['book_id'] ?>"><button name="action"
                                            value="cancel" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('ยกเลิกรายการจองนี้หรือไม่?')"><i
                                                class="bi bi-x-lg"></i> ยกเลิก</button></form>
                                    <?php elseif ($booking['status'] === 'จองแล้ว'): ?><form class="d-inline"
                                        method="post"><input type="hidden" name="book_id"
                                            value="<?= (int) $booking['book_id'] ?>"><button name="action"
                                            value="cancel" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('ยกเลิกรายการจองนี้หรือไม่?')"><i
                                                class="bi bi-x-lg"></i> ยกเลิก</button></form><?php else: ?><span
                                        class="text-secondary small">-</span><?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <p id="emptyState" class="d-none text-center text-secondary py-4 mb-0">ไม่พบรายการจอง</p>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <form method="post"><input type="hidden" name="action" value="create">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">เพิ่มการจอง</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">ผู้เข้าพัก</label><select class="form-select"
                                name="cus_id" required>
                                <option value="">-- เลือกผู้เข้าพัก --</option>
                                <?php while ($customer = $customers->fetch_assoc()): ?><option
                                    value="<?= (int) $customer['cus_id'] ?>">
                                    <?= htmlspecialchars($customer['cus_name']) ?></option><?php endwhile; ?>
                            </select></div>
                        <div class="mb-3"><label class="form-label">ห้องพัก</label><select class="form-select"
                                name="room_id" required>
                                <option value="">-- เลือกห้องพัก --</option>
                                <?php while ($room = $rooms->fetch_assoc()): ?><option
                                    value="<?= (int) $room['room_id'] ?>">ห้อง
                                    <?= htmlspecialchars($room['room_number']) ?> —
                                    <?= htmlspecialchars($room['room_type']) ?>
                                    (<?= number_format((float) $room['price']) ?> บาท/คืน)</option><?php endwhile; ?>
                            </select></div>
                        <div class="row g-3">
                            <div class="col-6"><label class="form-label">วันเข้าพัก</label><input class="form-control"
                                    type="date" name="check_in" min="<?= date('Y-m-d') ?>" required></div>
                            <div class="col-6"><label class="form-label">วันออก</label><input class="form-control"
                                    type="date" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                    required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0"><button type="button" class="btn btn-light"
                            data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึกการจอง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
    const search = document.getElementById('search'),
        statusFilter = document.getElementById('statusFilter'),
        emptyState = document.getElementById('emptyState');

    function filterRows() {
        let visible = 0;
        document.querySelectorAll('#bookingRows tr').forEach(row => {
            const show = (!search.value || row.textContent.toLowerCase().includes(search.value
                .toLowerCase())) && (!statusFilter.value || row.dataset.status === statusFilter.value);
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });
        emptyState.classList.toggle('d-none', visible !== 0);
    }
    search.addEventListener('input', filterRows);
    statusFilter.addEventListener('change', filterRows);
    </script>
</body>

</html>