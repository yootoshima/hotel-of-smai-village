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


   <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>