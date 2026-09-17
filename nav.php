<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid border rounded-4 p-3 bg-white shadow box-area" style="background: #025c15">
    <a href="index.php" class="brand-logo">
    <!-- วงกลม -->
    <div class="logo-circle">
        N
    </div>
    <!-- ชื่อ + คำข้างใต้ -->
    <div class="brand-text">
        <div class="brand-name">SMAI</div>
        <div class="brand-tagline">hotel</div>
    </div>
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3 text-wrap " style="width:17rem; font-family: 'Courier New', Courier, monospace;">
          <li class="nav-item">
            <a class="nav-link active mx-lg-2" aria-current="page" href="#">หน้าหลัก</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2 " href="#">สำรวจที่พัก</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="#">การจองของฉัน</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="#">สำหรับพาร์ทเนอร์</a>
          </li>
          <li class="nav-item">
            <a class="nav-link mx-lg-2" href="#">เกี่ยวกับเรา</a>
          </li>
          <a href="profile.php" class="profile-btn">

            <div class="profile-icon">
                 <?= mb_substr($_SESSION['username'], 0, 1) ?>
            </div>

            <span class="profile-name">
                <?= htmlspecialchars($_SESSION['username']) ?>
            </span>

        </a>

        </ul>
      </div>
    </div>
  </div>
</nav>

