<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GrandStay - สมัครสมาชิก</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="row border rounded-5 p-3 bg-white shadow box-area">

            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box"
                style="background: #025c15">
                <div class="image mb-3 feature-image">
                    <img src="img/hotel.png" class="img-fluid" style="width: 250px" />
                </div>
                <p class="text-white fs-2" style="font-family: 'Courier New', Courier, monospace;">สาดไปสมาชิก</p>
                <small class="text-white text-wrap text-center" style="width:17rem; font-family: 'Courier New', Courier, monospace;">
                    สมัครสมาชิกเพื่อรับสิทธิพิเศษ</small>
            </div>

            
            <div class="col-md-6 right-box">
                <form action="savemember.php" method="post">
                    <div class="row align-items-center">
                        <div class="header-text mb-4">
                            <center>
                                <h2 class="">สมัครสมาชิก </h2>
                                <p>สมัครสมาชิกเพื่อรับสิทธิพิเศษ</p>
                            </center>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg bg-light fs-6"
                                placeholder="ชื่อผู้ใช้งาน" name="username" id="username">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="รหัสผ่าน"
                                name="password" id="password">
                        </div>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control form-control-lg bg-light fs-6"
                                placeholder="หมายเลขโทรศัพท์" name="phone" id="username">
                        </div>
                        <div class="input-group mb-1">
                            <input type="number" class="form-control form-control-lg bg-light fs-6" placeholder="เลขบัตรประชาชน"
                                name="id" id="password">
                        </div>
                        <div class="input-group mb-5  d-flex justify-content-between">
                            
                        </div>
                        <div class="forgot">
                            <small><a href="/forgot">ลืมรหัสผ่าน?</a></small>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-lg btn-success w-100 fs-6"  >สมัครสมาชิก</button>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-lg btn-light border w-100 fs-6">
                                <img src="img/google.webp" style="width:20px;" class="me-2" /><small>Sign In with
                                    Google</small>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col text-secondary d-flex align-items-end">
                                <small>มีบัญชีแล้ว? <a href="index.html">Login</a></small>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        

    </div>

</body>

</html>