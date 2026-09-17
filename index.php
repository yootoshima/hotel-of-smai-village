<?php $googleError = $_GET['google_error'] ?? ''; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>hotel</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>

<body>
    <form action="login.php" id="login" method="post">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box"
                style="background: #025c15">
                <div class="image mb-3 feature-image">
                    <img src="img/hotel.png" class="img-fluid" style="width: 250px" />
                </div>
                <p class="text-white fs-2" style="font-family: 'Courier New', Courier, monospace;">สาดไปสมาชิก</p>
                <small class="text-white text-wrap text-center" style="width:17rem; font-family: 'Courier New', Courier, monospace;">Login
                    เพื่อเข้าสู่ระบบเพื่อใช้งานระบบจองห้องโรงแรม</small>
            </div>
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <div class="header-text mb-4">
                        <center>
                            <h2 class="">ยินดีต้อนรับกลับ </h2>
                            <p>พวกเรายินดีที่คุณกลับมา</p>
                        </center>
                    </div>
                    <?php if ($googleError): ?>
                    <div class="alert alert-danger py-2" role="alert"><?= htmlspecialchars($googleError) ?></div>
                    <?php endif; ?>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg bg-light fs-6"
                            placeholder="ชื่อผู้ใช้งาน" name="username" id="username">
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="รหัสผ่าน"
                            name="password" id="password">
                    </div>
                    <div class="input-group mb-5  d-flex justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="formCheck" />
                            <label for="formCheck" class="form-check-label text-secondary"><small>จดจำฉัน</small></label>
                        </div>
                    </div>
                    <div class="forgot">
                        <small><a href="forgot.php">ลืมรหัสผ่าน?</a></small>
                    </div>
                    <div class="input-group mb-3">
                        <button type="submit" class="btn btn-lg btn-success w-100 fs-6">Login</button>
                    </div>
                    <div class="input-group mb-3">
                        <a href="google_login.php" class="btn btn-lg btn-light border w-100 fs-6">
                            <img src="img/google.webp" style="width:20px;" class="me-2" /><small>Sign In with
                                Google</small>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col text-secondary d-flex align-items-end">
                            <small>ไม่มีบัญชี? <a href="regis.php">ลงทะเบียน</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </form>
    
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
