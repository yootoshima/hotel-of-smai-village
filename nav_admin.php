<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$adminName = $_SESSION['username'];
$currentPage = basename($_SERVER['PHP_SELF']);
$pages = [
    'index.php' => ['Dashboard', 'ภาพรวมระบบ'],
    'rooms.php' => ['ห้องพัก', 'จัดการห้องพัก'],
    'booking.php' => ['การจอง', 'จัดการการจอง'],
    'customers.php' => ['สมาชิก', 'จัดการสมาชิก'],
];
[$pageTitle, $pageSubtitle] = $pages[$currentPage] ?? ['Admin Panel', 'Ban Smaiy Hotel'];
$initial = mb_strtoupper(mb_substr($adminName, 0, 1));
?>
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="admin-navbar fixed-top" aria-label="แถบนำทางผู้ดูแลระบบ">
    <div class="container-fluid">
        <div class="admin-navbar-left">
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="เปิดหรือปิดเมนู"
                aria-controls="adminSidebar" aria-expanded="true">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>

            <a href="index.php" class="admin-brand" aria-label="ไปยังหน้า Dashboard">
                 <div class="admin-logo">
                    <i class="image feature-image"><img src="../img/hotel.png" class="img-fluid"
                            style="width: 250px" /></i>
                </div>
                <div class="admin-brand-text">
                    <div class="admin-brand-name">BAN SMAIY</div>
                    <div class="admin-brand-sub">HOTEL ADMIN</div>
                </div>
            </a>

            <div class="admin-page-title">
                <div class="page-title-main"><?= htmlspecialchars($pageTitle) ?></div>
                <div class="page-title-sub"><?= htmlspecialchars($pageSubtitle) ?></div>
            </div>
        </div>

        <div class="admin-navbar-right">
            <a href="booking.php" class="admin-notification" title="ดูการจอง" aria-label="ดูการจอง">
                <i class="bi bi-bell" aria-hidden="true"></i>
            </a>
            <div class="admin-divider" aria-hidden="true"></div>

            <div class="dropdown">
                <button class="admin-profile" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    aria-label="เมนูผู้ดูแลระบบ">
                    <div class="admin-avatar" aria-hidden="true"><?= htmlspecialchars($initial) ?></div>
                    <div class="admin-profile-info">
                        <div class="admin-name"><?= htmlspecialchars($adminName) ?></div>
                        <div class="admin-role">Administrator</div>
                    </div>
                    <i class="bi bi-chevron-down admin-arrow" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end admin-dropdown">
                    <li><div class="admin-dropdown-header"><div class="admin-avatar large" aria-hidden="true"><?= htmlspecialchars($initial) ?></div><div><strong><?= htmlspecialchars($adminName) ?></strong><small>Administrator</small></div></div></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="../logout.php" class="dropdown-item logout" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะออกจากระบบ?');><i class="bi bi-box-arrow-right"></i>ออกจากระบบ</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<aside class="admin-sidebar" id="adminSidebar" aria-label="เมนูผู้ดูแลระบบ">
    <div class="sidebar-header"><span>เมนูจัดการ</span></div>
    <nav class="sidebar-menu">
        <a href="index.php" class="sidebar-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" <?= $currentPage === 'index.php' ? 'aria-current="page"' : '' ?>><i class="bi bi-speedometer2" aria-hidden="true"></i><span>Dashboard</span></a>
        <a href="rooms.php" class="sidebar-link <?= $currentPage === 'rooms.php' ? 'active' : '' ?>" <?= $currentPage === 'rooms.php' ? 'aria-current="page"' : '' ?>><i class="bi bi-building" aria-hidden="true"></i><span>ห้องพัก</span></a>
        <a href="booking.php" class="sidebar-link <?= $currentPage === 'booking.php' ? 'active' : '' ?>" <?= $currentPage === 'booking.php' ? 'aria-current="page"' : '' ?>><i class="bi bi-calendar-check" aria-hidden="true"></i><span>การจอง</span></a>
        <a href="customers.php" class="sidebar-link <?= $currentPage === 'customers.php' ? 'active' : '' ?>" <?= $currentPage === 'customers.php' ? 'aria-current="page"' : '' ?>><i class="bi bi-people" aria-hidden="true"></i><span>สมาชิก</span></a>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
        const isCollapsed = sidebar.classList.toggle('collapsed');
        toggle.setAttribute('aria-expanded', String(!isCollapsed));
        toggle.querySelector('i').className = isCollapsed ? 'bi bi-list' : 'bi bi-x-lg';
    });
});
</script>
