<?php 
session_start();
if (empty($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
include(__DIR__ . '/../db.php');

$sql_rooms = "SELECT * FROM rooms"; 
$result_rooms = mysqli_query($conn, $sql_rooms);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้องพัก</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../nav.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">

</head>
<body class="bg-light">

    <?php include(__DIR__ . '/../nav.php'); ?>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center py-4">
        
        <div class="row bg-white shadow-lg rounded-5 overflow-hidden border w-100" style="max-width: 950px;">
            
            <div class="col-md-5 d-flex flex-column justify-content-center align-items-center text-white p-5 bg-success bg-gradient">
                <img src="../img/hotel.png" class="img-fluid mb-4 shadow-sm rounded-circle p-2 bg-white" style="max-width: 180px; aspect-ratio: 1/1; object-fit: contain;">
                <h3 class="fw-bold mb-2">โรงแรมบ้านสไมย์</h3>
                <div class="border-top border-light w-50 my-3 opacity-50"></div>
                <p class="text-center small px-3">
                    พื้นที่พักผ่อนที่คัดสรรด้วยความตั้งใจ สำหรับทุกจังหวะของการเดินทางของคุณ
                </p>
            </div>

            <div class="col-md-7 p-4 p-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-success">จองห้องพัก</h3>
                    <p class="text-secondary small">กรุณาระบุรายละเอียดการเข้าพักของคุณ</p>
                </div>

                <div class="alert alert-success py-2 text-center rounded-4 border-0 shadow-sm" role="alert">
                     <b>โปรโมชั่นพิเศษ!</b> เข้าพัก 5 คืนขึ้นไป รับส่วนลด 10%
                </div>

                <form action="booking_process.php" method="POST">
                    
                    <div class="form-floating mb-3 shadow-sm rounded-3">
                        <select class="form-select bg-light border-0" id="roomSelect" name="room_id" required>
                            <option value="" disabled selected>-- กรุณาเลือกห้องพัก --</option>
                            <?php 
                            while($room = mysqli_fetch_assoc($result_rooms)){
                                $r_id = $room['room_id'];
                                $r_num = isset($room['room_number']) ? $room['room_number'] : '-';
                                $r_type = isset($room['room_type']) ? $room['room_type'] : (isset($room['type']) ? $room['type'] : '-');
                                $r_price = isset($room['price']) ? $room['price'] : 0;

                                echo "<option value='{$r_id}'>หมายเลขห้อง {$r_num} - ประเภท: {$r_type} (ราคา {$r_price} บาท/คืน)</option>";
                            }
                            ?>
                        </select>
                        <label for="roomSelect" class="text-secondary">เลือกห้องพัก</label>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating shadow-sm rounded-3">
                                <input type="date" class="form-control bg-light border-0" id="checkIn" name="check_in" required>
                                <label for="checkIn" class="text-secondary">วันที่เช็คอิน</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating shadow-sm rounded-3">
                                <input type="date" class="form-control bg-light border-0" id="checkOut" name="check_out" required>
                                <label for="checkOut" class="text-secondary">วันที่เช็คเอาท์</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm mb-3 fs-5">
                        ยืนยันการจองห้องพัก
                    </button>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">เปลี่ยนใจ? 
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