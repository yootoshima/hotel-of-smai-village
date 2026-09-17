<?php 
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: rooms.php');
    exit;
}


$room_number = (int) ($_POST['room_number'] ?? 0);
$roomType = trim($_POST['room_type'] ?? '');
$price = (int) ($_POST['price'] ?? 0);
$status = trim($_POST['status'] ?? '');
$customerId = filter_input(INPUT_POST, 'cus_id', FILTER_VALIDATE_INT);
$checkIn = $_POST['check_in'] ?? '';
$checkOut = $_POST['check_out'] ?? '';

$check = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ?");
$check->bind_param("i", $room_number);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    exit("มีห้องหมายเลขนี้อยู่แล้ว");
}

if ($customerId && (!$checkIn || !$checkOut || $checkOut <= $checkIn)) {
    exit('กรุณาระบุวันเข้าพักและวันออกให้ถูกต้อง');
}

if ($customerId) {
    $status = 'ไม่ว่าง';
}

$conn->begin_transaction();

try {
    $insert = $conn->prepare('INSERT INTO rooms (room_number, room_type, price, status) VALUES (?, ?, ?, ?)');
    $insert->bind_param('isis', $room_number, $roomType, $price, $status);
    $insert->execute();

    if ($customerId) {
        $roomId = $conn->insert_id;
        $nights = (new DateTime($checkIn))->diff(new DateTime($checkOut))->days;
        $totalPrice = $price * $nights;
        $booking = $conn->prepare("INSERT INTO bookings (cus_id, room_id, check_in, check_out, total_price, status) VALUES (?, ?, ?, ?, ?, 'เข้าพัก')");
        $booking->bind_param('iissd', $customerId, $roomId, $checkIn, $checkOut, $totalPrice);
        $booking->execute();
    }

    $conn->commit();
    $result = true;
} catch (Throwable $error) {
    $conn->rollback();
    exit('เพิ่มห้องพักไม่สำเร็จ: ' . htmlspecialchars($error->getMessage()));
}

    if ($result) {
    header("Location: rooms.php");
    exit;
} else {
    exit("เพิ่มห้องไม่สำเร็จ: " . htmlspecialchars($conn->error));
}
?>
