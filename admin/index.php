<?php
session_start();
include '../db.php';
include 'dash.php';
$adminName = $_SESSION['username'] ?? 'Admin';
function roomPercent(int $count, int $total): int { return $total ? (int) round(($count / $total) * 100) : 0; }
function bookingBadge(string $status): string { return match ($status) { 'รอตรวจสอบ' => 'text-bg-warning', 'จองแล้ว' => 'text-bg-primary', 'เข้าพัก' => 'text-bg-success', 'ยกเลิก' => 'text-bg-danger', default => 'text-bg-secondary' }; }
?>
<!doctype html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>Dashboard | Ban Smaiy Hotel</title>
</head>

<body>
    <?php include(__DIR__ . '/../nav_admin.php'); ?>
    <main class="container mt-5 py-4 py-md-5" id="adminMain">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">Dashboard</h1>
                <p class="text-secondary mb-0">ภาพรวมการจัดการ Ban Smaiy Hotel</p>
            </div>
            <div class="text-secondary small"><i class="bi bi-calendar3 me-1"></i>อัปเดตข้อมูลล่าสุดจากระบบ</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-secondary mb-1">สมาชิกทั้งหมด</p>
                            <h3 class="fw-bold mb-0"><?= $totalCustomers ?></h3>
                        </div>
                        <div class="bg-success-subtle text-success rounded-3 p-3 fs-4"><i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0"><a href="customers.php"
                            class="small text-decoration-none">จัดการสมาชิก <i class="bi bi-arrow-right"></i></a></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-secondary mb-1">ห้องพักทั้งหมด</p>
                            <h3 class="fw-bold mb-0"><?= $totalRooms ?></h3>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-3 p-3 fs-4"><i class="bi bi-door-open"></i>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0"><a href="rooms.php"
                            class="small text-decoration-none">จัดการห้องพัก <i class="bi bi-arrow-right"></i></a></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-secondary mb-1">รายการจองทั้งหมด</p>
                            <h3 class="fw-bold mb-0"><?= $totalBookings ?></h3><small class="text-warning"><i
                                    class="bi bi-clock"></i> รอตรวจสอบ <?= $bookingStatuses['รอตรวจสอบ'] ?>
                                รายการ</small>
                        </div>
                        <div class="bg-warning-subtle text-warning rounded-3 p-3 fs-4"><i
                                class="bi bi-calendar-check"></i></div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0"><a href="booking.php"
                            class="small text-decoration-none">ดูรายการจอง <i class="bi bi-arrow-right"></i></a></div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="text-secondary mb-1">รายได้ที่ยืนยันแล้ว</p>
                            <h3 class="fw-bold mb-0">฿<?= number_format($totalRevenue, 2) ?></h3><small
                                class="text-secondary">จากการจองที่ยืนยัน/เข้าพัก</small>
                        </div>
                        <div class="bg-info-subtle text-info rounded-3 p-3 fs-4"><i class="bi bi-cash-stack"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="fw-bold mb-1">สถานะห้องพัก</h5><small class="text-secondary">รวม
                                    <?= $totalRooms ?> ห้อง</small>
                            </div><i class="bi bi-pie-chart fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="card-body px-4">
                        <?php foreach ([['ว่าง','success'], ['ไม่ว่าง','danger'], ['ซ่อมบำรุง','warning'], ['ปิดใช้งาน','secondary']] as [$status, $color]): $count = $roomStatuses[$status]; $percent = roomPercent($count, $totalRooms); ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2"><span><i
                                        class="bi bi-circle-fill text-<?= $color ?> me-2"></i><?= $status ?></span><strong><?= $count ?>
                                    ห้อง</strong></div>
                            <div class="progress" style="height: 8px">
                                <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percent ?>%"
                                    aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-1">การจัดการด่วน</h5><small
                            class="text-secondary">ทางลัดสำหรับงานที่ใช้บ่อย</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-4"><a href="rooms.php"
                                    class="btn btn-outline-success w-100 py-3"><i
                                        class="bi bi-plus-circle d-block fs-4 mb-2"></i>เพิ่มห้องพัก</a></div>
                            <div class="col-6 col-md-4"><a href="booking.php"
                                    class="btn btn-outline-primary w-100 py-3"><i
                                        class="bi bi-calendar-plus d-block fs-4 mb-2"></i>เพิ่มการจอง</a></div>
                            <div class="col-6 col-md-4"><a href="customers.php"
                                    class="btn btn-outline-warning w-100 py-3"><i
                                        class="bi bi-person-plus d-block fs-4 mb-2"></i>เพิ่มสมาชิก</a></div>
                            <div class="col-6 col-md-4"><a href="booking.php?status=รอตรวจสอบ"
                                    class="btn btn-outline-secondary w-100 py-3"><i
                                        class="bi bi-hourglass-split d-block fs-4 mb-2"></i>รอตรวจสอบ</a></div>
                            <div class="col-6 col-md-4"><a href="rooms.php" class="btn btn-outline-info w-100 py-3"><i
                                        class="bi bi-door-open d-block fs-4 mb-2"></i>ดูห้องพัก</a></div>
                            <div class="col-6 col-md-4"><a href="booking.php" class="btn btn-outline-dark w-100 py-3"><i
                                        class="bi bi-list-check d-block fs-4 mb-2"></i>รายการจอง</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">การจองล่าสุด</h5><small class="text-secondary">5
                        รายการล่าสุดจากระบบ</small>
                </div><a href="booking.php" class="btn btn-sm btn-outline-success">ดูทั้งหมด</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">ผู้จอง</th>
                                <th>ห้องพัก</th>
                                <th>วันเข้าพัก</th>
                                <th>วันออก</th>
                                <th class="text-end">ยอดรวม</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody><?php if ($recentBookings->num_rows === 0): ?><tr>
                                <td colspan="6" class="text-center text-secondary py-4">ยังไม่มีรายการจอง</td>
                            </tr><?php else: while ($booking = $recentBookings->fetch_assoc()): ?><tr>
                                <td class="px-4 fw-semibold"><?= htmlspecialchars($booking['cus_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['room_number'] ?? '-') ?></td>
                                <td><?= $booking['check_in'] ? date('d/m/Y', strtotime($booking['check_in'])) : '-' ?>
                                </td>
                                <td><?= $booking['check_out'] ? date('d/m/Y', strtotime($booking['check_out'])) : '-' ?>
                                </td>
                                <td class="text-end">฿<?= number_format((float) $booking['total_price'], 2) ?></td>
                                <td><span
                                        class="badge <?= bookingBadge($booking['status']) ?>"><?= htmlspecialchars($booking['status']) ?></span>
                                </td>
                            </tr><?php endwhile; endif; ?></tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>