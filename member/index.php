<?php 
session_start();
if (empty($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}
include(__DIR__ . '/../db.php');
    $sql="select * from customers where cus_name='$_SESSION[username]'";
    $result=mysqli_query($conn,$sql);
    $read=mysqli_fetch_assoc($result);
    $name=$read['cus_name'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../nav.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        #booking-section {
            scroll-margin-top: 120px;
        }
    </style>
</head>
<body>
   <?php include(__DIR__ . '/../nav.php'); ?>
   <div class="container d-flex justify-content-center align-items-center " style="margin-top: 140px">
    <div class="row border rounded-5 p-3 bg-white shadow box-area" >
        <div class="col-md-12 hero  rounded-4 d-flex justify-content-center align-items-center flex-column left-box"style="background: #025c15">
                <div class="image mb-3 feature-image">
                    <img src="../img/hotel.png" class="img-fluid" style="width: 250px" />
                </div>
                <div>
                    <div class="header-text text-white mb-4 align-items-center">
                       <center>
                        <h2>จองในแบบที่คุณอยากจอง</h2>
                        <p>พวกเรายินดีที่จะบริการจองห้องพัก</p>
                        <small>พื้นที่พักผ่อนที่คัดสรรด้วยความตั้งใจ สำหรับทุกจังหวะของการเดินทาง</small>
                       </center> 
                    </div>
                    <center>
                        <a href="booking.php#booking-section" class="btn-book btn btn-light align-items-center justify-content-center shadow mb-3 rounded-pill px-4 py-2">
                            จองเลย
                        </a>
                    </center>
                </div>
        </div>
    </div>
   </div> 

    <hr style="margin-top: 140px">

    <?php 
    // Query ดึงข้อมูลห้องพักทั้งหมดจากตาราง rooms พร้อมยอดชำระสุทธิจากการจองล่าสุดของห้องนั้น
    $sql2 = "SELECT r.*, b.total_price, b.status AS booking_status,
                    DATEDIFF(b.check_out, b.check_in) AS nights
             FROM rooms r
             LEFT JOIN (
                 SELECT room_id, MAX(book_id) AS latest_book_id
                 FROM bookings
                 GROUP BY room_id
             ) latest ON latest.room_id = r.room_id
             LEFT JOIN bookings b ON b.book_id = latest.latest_book_id
             ORDER BY r.room_number ASC";
    $result2 = mysqli_query($conn, $sql2);
    $i = 1;
    ?>

    <div id="booking-section" class="container my-4">
        <h3 class="mb-3">รายการห้องพัก</h3>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">รหัสห้อง</th>
                    <th>หมายเลขห้อง</th>
                    <th>ประเภทห้อง</th>
                    <th class="text-end">ราคา/คืน</th>
                    <th class="text-center">จำนวนคืน</th>
                    <th class="text-end">ราคาที่ต้องจ่าย</th>
                    <th class="text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($read2 = mysqli_fetch_assoc($result2)) { 
                    $room_id = $read2['room_id'];
                    $room_number = $read2['room_number'];
                    $room_type = $read2['room_type'];
                    $price = $read2['price'];
                    $status = $read2['status'];
                    $payable_total = !empty($read2['total_price']) ? (float) $read2['total_price'] : 0;
                    $nights = !empty($read2['nights']) ? (int) $read2['nights'] : 0;
                ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td class="text-center"><?php echo $room_id; ?></td>
                    <td><?php echo $room_number; ?></td>
                    <td><?php echo $room_type; ?></td>
                    <td class="text-end"><?php echo number_format($price); ?> บาท</td>
                    <td class="text-center"><?php echo $nights > 0 ? $nights : '-'; ?> คืน</td>
                    <td class="text-end"><?php echo number_format($payable_total, 2); ?> บาท</td>
                    <td class="text-center">
                        <span class="badge <?php echo ($status == 'available' || $status == 'ว่าง') ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $status; ?>
                        </span>
                    </td>
                </tr>
                <?php 
                } 
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>