<?php 
session_start();
include "db.php";

$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM customers WHERE cus_name = '$user'";


$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $customer = $result->fetch_assoc();

    if (password_verify($pass, $customer['password'])) {

        $_SESSION['username'] = $customer['cus_name'];
        $_SESSION['role'] = $customer['role'];
        $_SESSION['id'] = $customer['id_card'];

        if (isset($_POST['remember'])) {
            setcookie(
                "username",
                $user,
                time() + (86400 * 30),
                "/"
            );
        }

        echo "<script>
                alert('เข้าสู่ระบบสำเร็จ');
                window.location.href = '/hotel-of-smai-village/member/index.php';
              </script>";

    } else {

        echo "<script>
                alert('รหัสผ่านไม่ถูกต้อง');
                window.history.back();
              </script>";

    }

} else {

    echo "<script>
            alert('ไม่พบ Username นี้');
            window.history.back();
          </script>";

}




?>