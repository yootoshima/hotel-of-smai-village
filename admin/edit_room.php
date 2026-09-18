<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $roomNumber = filter_input(INPUT_POST, 'room_number', FILTER_VALIDATE_INT);
    $roomType = trim($_POST['room_type'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
    $status = trim($_POST['status'] ?? '');
    $customerId = filter_input(INPUT_POST, 'cus_id', FILTER_VALIDATE_INT);

    if (!$roomId || !$roomNumber || $roomType === '' || $price === false || $price === null || $status === '') {
        header('Location: rooms.php?error=invalid');
        exit;
    }

    $duplicate = $conn->prepare('SELECT room_id FROM rooms WHERE room_number = ? AND room_id != ?');
    $duplicate->bind_param('ii', $roomNumber, $roomId);
    $duplicate->execute();

    if ($duplicate->get_result()->num_rows > 0) {
        header('Location: rooms.php?error=duplicate');
        exit;
    }

    if ($customerId && $status === 'ว่าง') {
        $status = 'ไม่ว่าง';
    }

    $conn->begin_transaction();

    try {
        $update = $conn->prepare('UPDATE rooms SET room_number = ?, room_type = ?, price = ?, status = ? WHERE room_id = ?');
        $update->bind_param('isisi', $roomNumber, $roomType, $price, $status, $roomId);
        $update->execute();

        if ($customerId) {
            $booking = $conn->prepare("SELECT book_id FROM bookings WHERE room_id = ? AND status IN ('รอตรวจสอบ', 'จองแล้ว', 'เข้าพัก') ORDER BY book_id DESC LIMIT 1");
            $booking->bind_param('i', $roomId);
            $booking->execute();
            $bookingResult = $booking->get_result();

            if ($bookingResult->num_rows > 0) {
                $bookingRow = $bookingResult->fetch_assoc();
                $updateBooking = $conn->prepare('UPDATE bookings SET cus_id = ? WHERE book_id = ?');
                $updateBooking->bind_param('ii', $customerId, $bookingRow['book_id']);
                $updateBooking->execute();
            }
        }

        $conn->commit();
        header('Location: rooms.php');
        exit;
    } catch (Throwable $e) {
        $conn->rollback();
        header('Location: rooms.php?error=update');
        exit;
    }
}

$roomId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$roomId) {
    header('Location: rooms.php');
    exit;
}

$room = $conn->prepare('SELECT room_id, room_number, room_type, price, status FROM rooms WHERE room_id = ?');
$room->bind_param('i', $roomId);
$room->execute();
$roomResult = $room->get_result();

if ($roomResult->num_rows === 0) {
    header('Location: rooms.php');
    exit;
}

$roomData = $roomResult->fetch_assoc();

$customers = $conn->query("SELECT cus_id, cus_name FROM customers WHERE role = 'member' ORDER BY cus_name ASC");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../admin.css">
    <title>แก้ไขห้องพัก</title>
</head>
<body>
    <?php include __DIR__ . '/../nav_admin.php'; ?>

    <main class="container mt-5 py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h1 class="h3 mb-1 fw-bold">แก้ไขห้องพัก</h1>
                                <p class="text-secondary mb-0">ปรับปรุงข้อมูลห้องพัก</p>
                            </div>
                            <a href="rooms.php" class="btn btn-outline-secondary btn-sm">กลับ</a>
                        </div>

                        <form method="post" action="edit_room.php">
                            <input type="hidden" name="room_id" value="<?= (int) $roomData['room_id']; ?>">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">หมายเลขห้อง</label>
                                    <input type="number" name="room_number" value="<?= (int) $roomData['room_number']; ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ราคาต่อคืน (บาท)</label>
                                    <input type="number" name="price" min="0" value="<?= (int) $roomData['price']; ?>" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">ประเภทห้อง</label>
                                    <select name="room_type" class="form-select" required>
                                        <?php
                                        $types = ['ปกติ (เตียงเดี่ยว)', 'VIP (เตียงเดี่ยว)', 'ปกติ (เตียงคู่)', 'VIP (เตียงคู่)'];
                                        foreach ($types as $type) {
                                            $selected = $roomData['room_type'] === $type ? 'selected' : '';
                                            echo "<option value=\"$type\" $selected>$type</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">สถานะห้อง</label>
                                    <select name="status" class="form-select" required>
                                        <?php
                                        $statuses = ['ว่าง', 'ไม่ว่าง', 'ซ่อมบำรุง', 'ปิดใช้งาน'];
                                        foreach ($statuses as $statusOption) {
                                            $selected = $roomData['status'] === $statusOption ? 'selected' : '';
                                            echo "<option value=\"$statusOption\" $selected>$statusOption</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label for="cus_id" class="form-label">ผู้เข้าพัก</label>
                                    <select id="cus_id" name="cus_id" class="form-select w-100">
                                        <option value="">-- เลือกผู้เข้าพัก --</option>
                                        <?php while ($customer = $customers->fetch_assoc()) { ?>
                                            <option value="<?= (int) $customer['cus_id'] ?>"><?= htmlspecialchars($customer['cus_name']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="rooms.php" class="btn btn-light">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary px-4">บันทึกข้อมูล</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
