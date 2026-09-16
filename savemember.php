<?php 

include 'db.php';

$user = $_POST['username'];
$pass = $_POST['password'];
$phone = $_POST['phone'];
$id = $_POST['id'];

// ตรวจสอบ Username
if (strlen($user) < 4) {
    echo "Username ต้องมีอย่างน้อย 4 ตัว";
    exit();
}

// ตรวจสอบ Password
if (strlen($pass) < 6) {
    echo "Password ต้องมีอย่างน้อย 6 ตัว";
    exit();
}

// ตรวจสอบเบอร์โทร
if (strlen($phone) != 10) {
    echo "เบอร์โทรต้องมี 10 ตัว";
    exit();
}

// ตรวจสอบเลขบัตร
if (strlen($id) != 13) {
    echo "เลขบัตรประชาชนต้องมี 13 ตัว";
    exit();
}


$passHash = password_hash($pass, PASSWORD_DEFAULT);

$sql = "insert into customers (cus_name,phone,id_card,password,role) values('$user','$phone','$id','$passHash','member')";

$result = $conn->query($sql);

    if($result){
        echo "สมัครสมาชิกสำเร็จ";
        echo"<meta http-equiv='refresh' content='1;url=index.php'>";
        
    }else{
        echo "สมัครสมาชิกไม่สำเร็จ";
    }
?>