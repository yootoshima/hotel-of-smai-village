<?php
session_start();
include('../db.php');


$room_id = $_POST['room_id'];
$check_in = $_POST['check_in'];
$check_out = $_POST['check_out'];
$username = $_SESSION['username'];

// ดึงข้อมูล customer_id จากฐานข้อมูลโดยใช้ชื่อผู้ใช้งาน
$sql_cus = "SELECT cus_id FROM customers WHERE cus_name = '$username'";
$result_cus = mysqli_query($conn, $sql_cus);
$row_cus = mysqli_fetch_assoc($result_cus);
$customer_id = $row_cus['cus_id'];

//p and s
$sql_room = "SELECT price, status FROM rooms WHERE room_id = '$room_id'";
$result_room = mysqli_query($conn, $sql_room);
$room = mysqli_fetch_assoc($result_room);

$room_price = $room['price'];
$room_status = $room['status'];


if ($room_status == "ไม่ว่าง" || $room_status == "unavailable") {
    echo "<script>
            alert('ขออภัย ห้องนี้ไม่สามารถจองได้ เนื่องจากสถานะไม่ว่าง');
            window.history.back();
          </script>";
    exit();
}

// คำนวณจำนวนคืน
$in = strtotime($check_in);
$out = strtotime($check_out);
$nights = ($out - $in) / (60 * 60 * 24);

if ($nights < 1) {
    echo "<script>
            alert('ข้อมูลวันผิดพลาด กรุณาเลือกวันเข้าพักอย่างน้อย 1 คืน'); 
            window.history.back();
          </script>";
    exit();
}



$base_price = $room_price * $nights; 


if ($nights >= 5) {
    $discount = $base_price * 0.10; 
} else {
    $discount = 0;
}

$total_price = $base_price - $discount;


$sql_insert = "INSERT INTO bookings (cus_id, room_id, check_in, check_out, total_price, status)VALUES ('$customer_id', '$room_id', '$check_in', '$check_out', '$total_price', 'จองแล้ว')";

if (mysqli_query($conn, $sql_insert)) {
    
    $sql_update = "UPDATE rooms SET status = 'ไม่ว่าง' WHERE room_id = '$room_id'";
    mysqli_query($conn, $sql_update);
    
    echo "<script>
            alert('จองสำเร็จ! คุณพัก $nights คืน ยอดชำระสุทธิ $total_price บาท');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "<script>alert('เกิดข้อผิดพลาดในการจอง'); window.history.back();</script>";
}
?>