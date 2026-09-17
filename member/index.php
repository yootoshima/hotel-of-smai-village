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
                        <a href="booking.php" class="btn-book btn btn-light align-items-center justify-content-center shadow mb-3 rounded-pill px-4 py-2" ">
                            จองเลย
                        </a>
                    </center>
                </div>
        </div>
    </div>
   </div> 

    <hr style="margin-top: 140px">

    <?php 
    // Query ดึงข้อมูลห้องพักทั้งหมดจากตาราง rooms
    $sql2 = "SELECT * FROM rooms";
    $result2 = mysqli_query($conn, $sql2);
    $i = 1; // กำหนดตัวแปรสำหรับแสดงลำดับ
    ?>

    <div class="container my-4">
        <h3 class="mb-3">รายการห้องพัก</h3>
        <table class="table table-striped table-hover align-middle">
            <thead class="table" style="background: #025c15">
                <tr>
                    <th class="text-center">ลำดับ</th>
                    <th class="text-center">รหัสห้อง</th>
                    <th>หมายเลขห้อง</th>
                    <th>ประเภทห้อง</th>
                    <th class="text-end">ราคา/คืน</th>
                    <th class="text-center">สถานะ</th>
                    <th class="text-center">แก้ไขข้อมูล</th>
                    <th class="text-center">ลบข้อมูล</th>
                </tr>
            </thead>
            <tbody>
                <?php while($read2 = mysqli_fetch_assoc($result2)) { 
                    $room_id = $read2['room_id'];
                    $room_number = $read2['room_number'];
                    $room_type = $read2['room_type'];
                    $price = $read2['price'];
                    $status = $read2['status'];
                ?>
                <tr>
                    <td class="text-center"><?php echo $i++; ?></td>
                    <td class="text-center"><?php echo $room_id; ?></td>
                    <td><?php echo $room_number; ?></td>
                    <td><?php echo $room_type; ?></td>
                    <td class="text-end"><?php echo number_format($price); ?> บาท</td>
                    <td class="text-center">
                        <span class="badge <?php echo ($status == 'available' || $status == 'ว่าง') ? 'bg-success' : 'bg-secondary'; ?>">
                            <?php echo $status; ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="edit_room.php?id=<?php echo $room_id; ?>" class="btn btn-primary btn-sm">แก้ไข</a>
                    </td>
                    <td class="text-center">
                        <a href="del_room.php?id=<?php echo $room_id; ?>" 
                           onclick="return confirm('ท่านต้องการลบข้อมูลห้องพักนี้หรือไม่?')" 
                           class="btn btn-danger btn-sm">ลบ</a>
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