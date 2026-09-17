<?php 
session_start();
include "db.php";

$user = trim($_POST['username'] ?? '');
$pass = $_POST['password'] ?? '';

$sql = $conn->prepare('SELECT * FROM customers WHERE cus_name = ? LIMIT 1');
$sql->bind_param('s', $user);
$sql->execute();
$customer = $sql->get_result()->fetch_assoc();

if ($customer) {

    if (password_verify($pass, $customer['password'])) {

        // เก็บข้อมูลลง Session
        $_SESSION['username'] = $customer['cus_name'];
        $_SESSION['role'] = $customer['role'];
        $_SESSION['id'] = $customer['id_card'];

        // Remember Me
        if (isset($_POST['remember'])) {
            setcookie(
                "username",
                $user,
                time() + (86400 * 30),
                "/"
            );
        }

        // เช็ก Role
        if ($customer['role'] === 'admin') {

            // ถ้าเป็น Admin
            echo "<script>
                    alert('เข้าสู่ระบบ Admin สำเร็จ');
                    window.location.href = '/hotel-of-smai-village/admin/index.php';
                  </script>";

        } else {

            // ถ้าเป็น Member
            echo "<script>
                    alert('เข้าสู่ระบบสำเร็จ');
                    window.location.href = '/hotel-of-smai-village/member/index.php';
                  </script>";
        }

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
