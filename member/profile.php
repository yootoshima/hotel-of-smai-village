<?php 
session_start();
if (empty($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
include(__DIR__ . '/../db.php');

$username = mysqli_real_escape_string($conn, $_SESSION['username']);

$sql = "SELECT * FROM customers WHERE cus_name = '$username'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$cus_name = $user['cus_name'] ?? '';
$phone = $user['phone'] ?? '';
$id_card = $user['id_card'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body class="bg-light">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center py-4">
        
        <div class="row bg-white shadow-lg rounded-5 overflow-hidden border w-100" style="max-width: 950px;">
            
            <div class="col-md-5 d-flex flex-column justify-content-center align-items-center text-white p-5 bg-success bg-gradient">
                <img src="../img/hotel.png" class="img-fluid mb-4 shadow-sm rounded-circle p-2 bg-white" style="max-width: 180px; aspect-ratio: 1/1; object-fit: contain;">
                <h3 class="fw-bold mb-2">โรงแรมบ้านสไมย์</h3>
                <div class="border-top border-light w-50 my-3 opacity-50"></div>
                <p class="text-center small px-3">
                    แก้ไขข้อมูลบัญชีผู้ใช้งานของคุณให้เป็นปัจจุบัน
                </p>
            </div>

            <div class="col-md-7 p-4 p-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-success">แก้ไขข้อมูลส่วนตัว</h3>
                    <p class="text-secondary small">อัปเดตข้อมูลส่วนตัวของคุณในระบบ</p>
                </div>

                <form action="edit_profile_process.php" method="POST">
                    
                    <div class="form-floating mb-3 shadow-sm rounded-3">
                        <input type="text" class="form-control bg-light border-0" id="cusName" name="cus_name" value="<?php ($cus_name); ?>" required>
                        <label for="cusName" class="text-secondary">ชื่อผู้ใช้งาน</label>
                    </div>

                    <div class="form-floating mb-3 shadow-sm rounded-3">
                        <input type="text" class="form-control bg-light border-0" id="phone" name="phone" value="<?php ($phone); ?>" maxlength="10" required>
                        <label for="phone" class="text-secondary">หมายเลขโทรศัพท์</label>
                    </div>

                    <div class="form-floating mb-3 shadow-sm rounded-3">
                        <input type="text" class="form-control bg-light border-0" id="idCard" name="id_card" value="<?php ($id_card); ?>" maxlength="13">
                        <label for="idCard" class="text-secondary">เลขบัตรประจำตัวประชาชน</label>
                    </div>

                    <div class="form-floating mb-4 shadow-sm rounded-3">
                        <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="รหัสผ่านใหม่">
                        <label for="password" class="text-secondary">รหัสผ่านใหม่ (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</label>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm mb-3 fs-5">
                        บันทึกการแก้ไขข้อมูล
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">ไม่ต้องการแก้ไข? 
                            <a href="index.php" class="text-success fw-bold text-decoration-none border-bottom border-success pb-1">กลับไปหน้าหลัก</a>
                        </small>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>