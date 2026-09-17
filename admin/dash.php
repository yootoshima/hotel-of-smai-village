<?php
$totalCustomers = (int) $conn->query("SELECT COUNT(*) AS total FROM customers WHERE role = 'member'")->fetch_assoc()['total'];
$totalRooms = (int) $conn->query('SELECT COUNT(*) AS total FROM rooms')->fetch_assoc()['total'];
$totalBookings = (int) $conn->query('SELECT COUNT(*) AS total FROM bookings')->fetch_assoc()['total'];
$totalRevenue = (float) $conn->query("SELECT COALESCE(SUM(total_price), 0) AS total FROM bookings WHERE status IN ('จองแล้ว', 'เข้าพัก', 'เสร็จสิ้น')")->fetch_assoc()['total'];

$roomStatuses = ['ว่าง' => 0, 'ไม่ว่าง' => 0, 'ซ่อมบำรุง' => 0, 'ปิดใช้งาน' => 0];
$roomStatusResult = $conn->query('SELECT status, COUNT(*) AS total FROM rooms GROUP BY status');
while ($row = $roomStatusResult->fetch_assoc()) {
    if (array_key_exists($row['status'], $roomStatuses)) $roomStatuses[$row['status']] = (int) $row['total'];
}

$bookingStatuses = ['รอตรวจสอบ' => 0, 'จองแล้ว' => 0, 'เข้าพัก' => 0, 'เสร็จสิ้น' => 0, 'ยกเลิก' => 0];
$bookingStatusResult = $conn->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status');
while ($row = $bookingStatusResult->fetch_assoc()) {
    if (array_key_exists($row['status'], $bookingStatuses)) $bookingStatuses[$row['status']] = (int) $row['total'];
}

$recentBookings = $conn->query("SELECT b.book_id, b.check_in, b.check_out, b.total_price, b.status, c.cus_name, r.room_number FROM bookings b LEFT JOIN customers c ON c.cus_id = b.cus_id LEFT JOIN rooms r ON r.room_id = b.room_id ORDER BY b.book_id DESC LIMIT 5");
?>