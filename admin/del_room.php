<?php
include '../db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit('ไม่พบรหัสห้องพักที่ต้องการลบ');
}

$stmt = $conn->prepare(
    "DELETE FROM rooms WHERE room_id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: rooms.php");
    exit;
}

echo "ลบห้องไม่สำเร็จ: " . $conn->error;
?>
