<?php
session_start();
if (empty($_SESSION['username']) || empty($_SESSION['cus_id'])) {
    header("Location: ../index.php");
    exit();
}
include(__DIR__ . '/../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = (int) $_SESSION['cus_id'];
    $cus_name = trim($_POST['cus_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $id_card = trim($_POST['id_card'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($cus_name === '' || $phone === '' || $id_card === '') {
        echo "<script>
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                window.location.href='profile.php';
              </script>";
        exit;
    }

    if ($password !== '') {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE customers SET cus_name = ?, phone = ?, id_card = ?, password = ? WHERE cus_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $cus_name, $phone, $id_card, $passwordHash, $customerId);
    } else {
        $sql = "UPDATE customers SET cus_name = ?, phone = ?, id_card = ? WHERE cus_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $cus_name, $phone, $id_card, $customerId);
    }

    if ($stmt->execute()) {
        $_SESSION['username'] = $cus_name;

        session_unset();
        session_destroy();

        echo "<script>
                alert('บันทึกการแก้ไขข้อมูลสำเร็จ! กรุณาเข้าสู่ระบบใหม่อีกครั้ง');
                window.location.href='../index.php';
              </script>";
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error . "');
                window.location.href='profile.php';
              </script>";
    }
}
?>