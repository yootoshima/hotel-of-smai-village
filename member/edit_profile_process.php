<?php
session_start();
if (empty($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
include(__DIR__ . '/../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    $cus_name = mysqli_real_escape_string($conn, $_POST['cus_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $id_card = mysqli_real_escape_string($conn, $_POST['id_card']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);


    if (!empty($password)) {
        $sql = "UPDATE customers SET 
                cus_name = '$cus_name', 
                phone = '$phone', 
                id_card = '$id_card', 
                password = '$password' 
                WHERE cus_name = '$old_username'";
    } else {
        $sql = "UPDATE customers SET 
                cus_name = '$cus_name', 
                phone = '$phone', 
                id_card = '$id_card' 
                WHERE cus_name = '$old_username'";
    }


    if (mysqli_query($conn, $sql)) {


        session_unset();
        session_destroy();

        
        echo "<script>
                alert('บันทึกการแก้ไขข้อมูลสำเร็จ! กรุณาเข้าสู่ระบบใหม่อีกครั้ง');
                window.location.href='../index.php';
              </script>";
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn) . "');
                window.location.href='profile.php';
              </script>";
    }
}
?>