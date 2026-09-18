<?php
session_start();
include '../db.php';
$sqlCustomers = " SELECT cus_id, cus_name FROM customers WHERE role = 'member' ORDER BY cus_name ASC";

$resultCustomers = $conn->query($sqlCustomers);

$adminName = $_SESSION['username'] ?? 'Admin';




?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <?php include(__DIR__ . '/../nav_admin.php'); ?>
    <?php 
    
$sql = " SELECT r.room_id, r.room_number, r.room_type, r.price, r.status, c.cus_name FROM rooms AS r LEFT JOIN bookings AS b ON r.room_id = b.room_id AND b.status = 'เข้าพัก' LEFT JOIN customers AS c ON b.cus_id = c.cus_id ORDER BY r.room_number ASC";

$result = mysqli_query($conn, $sql);
    ?>
    <main class="container mt-5 py-4 py-md-5" id="adminMain">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between mb-4">
            <div>
                <h1 class="page-title h2 mb-1">จัดการห้องพัก</h1>
                <p class="text-secondary mb-0">
                    เพิ่ม แก้ไข ค้นหา และตรวจสอบสถานะห้องพัก
                </p>
            </div>
            <button class="btn btn-primary px-3 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#roomModal"
                onclick="openAdd()">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มห้องพัก
            </button>
        </div>
        <div class="row g-3 mb-4" id="stats"></div>
        <section class="panel bg-white p-3 p-md-4">
            <div class="row g-2 mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span><input id="search"
                            class="form-control border-start-0" placeholder="ค้นหาหมายเลขห้อง หรือประเภทห้อง" />
                    </div>
                </div>
                <!-- <div class="col-6 col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">ทุกสถานะ</option>
                        <option>ว่าง</option>
                        <option>ไม่ว่าง</option>
                        <option>ซ่อมบำรุง</option>
                        <option>ปิดใช้งาน</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select class="form-select" id="floorFilter">
                        <option value="">ทุกชั้น</option>
                        <option value="1">ชั้น 1</option>
                        <option value="2">ชั้น 2</option>
                        <option value="3">ชั้น 3</option>
                    </select>
                </div> -->
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">ห้อง</th>
                            <th class="text-center">ประเภท</th>

                            <th class="text-center">ราคา/คืน</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">ผู้เข้าพัก</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody id="roomRows">
                        <?php while($read2 = mysqli_fetch_assoc($result)) { ?>
                        <tr>

                            <td class="text-center"><?php echo htmlspecialchars($read2['room_number']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($read2['room_type']); ?></td>
                            <td class="text-center"><?php echo number_format((float) $read2['price']); ?> บาท</td>
                            <td class="text-center">
                                <span
                                    class="badge <?php echo ($read2['status'] == 'available' || $read2['status'] == 'ว่าง') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo htmlspecialchars($read2['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?= $read2['cus_name'] ? htmlspecialchars($read2['cus_name']) : '-' ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-sm"
                                    data-room-id="<?php echo (int) $read2['room_id']; ?>"
                                    data-room-number="<?php echo htmlspecialchars($read2['room_number'], ENT_QUOTES); ?>"
                                    data-room-type="<?php echo htmlspecialchars($read2['room_type'], ENT_QUOTES); ?>"
                                    data-room-price="<?php echo (float) $read2['price']; ?>"
                                    data-room-status="<?php echo htmlspecialchars($read2['status'], ENT_QUOTES); ?>"
                                    onclick="openEditModal(this)">แก้ไข</button>
                                <a href="del_room.php?id=<?php echo (int) $read2['room_id']; ?>"
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

        </section>
    </main>
    <div class="modal fade" id="roomModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <form id="roomForm" method="post" action="save_room.php">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalTitle">เพิ่มห้องพัก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <input type="hidden" id="editId" />
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">หมายเลขห้อง</label><input name="room_number" required
                                    id="number" class="form-control" placeholder="เช่น 101" />
                            </div>
                            <!-- <div class="col-md-6">
                                <label class="form-label">วันเข้าพัก</label>
                                <input name="check_in" type="date" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันออก</label>
                                <input name="check_out" type="date" class="form-control" />
                            </div> -->


                            <div class="col-md-6">
                                <label class="form-label">ราคาต่อคืน (บาท)</label><input name="price" required min="0"
                                    type="number" id="price" class="form-control" placeholder="650" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ประเภทห้อง</label><select name="room_type" required id="type"
                                    class="form-select">
                                    <option value='ปกติ (เตียงเดี่ยว)'>ปกติ (เตียงเดี่ยว)</option>
                                    <option value='VIP (เตียงเดี่ยว)'>VIP (เตียงเดี่ยว)</option>
                                    <option value='ปกติ (เตียงคู่)'>ปกติ (เตียงคู่)</option>
                                    <option value='VIP (เตียงคู่)'>VIP (เตียงคู่)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">สถานะห้อง</label><select name="status" required id="status"
                                    class="form-select">
                                    <option value='ว่าง'>ว่าง</option>
                                    <option value='ไม่ว่าง'>ไม่ว่าง</option>
                                    <option value='ซ่อมบำรุง'>ซ่อมบำรุง</option>
                                    <option value='ปิดใช้งาน'>ปิดใช้งาน</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="cus_id" class="form-label">ผู้เข้าพัก</label>

                                <select id="cus_id" name="cus_id" class="form-select w-100">
                                    <option value="">-- เลือกผู้เข้าพัก --</option>

                                    <?php while ($customer = $resultCustomers->fetch_assoc()) { ?>
                                    <option value="<?= $customer['cus_id'] ?>">
                                        <?= htmlspecialchars($customer['cus_name']) ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            ยกเลิก</button><button class="btn btn-primary px-3">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="roomModal2" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <form id="roomForm2" method="post" action="edit_room.php">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalTitle">เพิ่มห้องพัก</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <input type="hidden" name="room_id" />
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">หมายเลขห้อง</label><input name="room_number" required
                                    id="number" class="form-control" placeholder="เช่น 101" />
                            </div>


                            <div class="col-md-6">
                                <label class="form-label">ราคาต่อคืน (บาท)</label><input name="price" required min="0"
                                    type="number" id="price" class="form-control" placeholder="650" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ประเภทห้อง</label><select name="room_type" required id="type"
                                    class="form-select">
                                    <option value='ปกติ (เตียงเดี่ยว)'>ปกติ (เตียงเดี่ยว)</option>
                                    <option value='VIP (เตียงเดี่ยว)'>VIP (เตียงเดี่ยว)</option>
                                    <option value='ปกติ (เตียงคู่)'>ปกติ (เตียงคู่)</option>
                                    <option value='VIP (เตียงคู่)'>VIP (เตียงคู่)</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">สถานะห้อง</label><select name="status" required id="status"
                                    class="form-select">
                                    <option value='ว่าง'>ว่าง</option>
                                    <option value='ไม่ว่าง'>ไม่ว่าง</option>
                                    <option value='ซ่อมบำรุง'>ซ่อมบำรุง</option>
                                    <option value='ปิดใช้งาน'>ปิดใช้งาน</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="cus_id" class="form-label">ผู้เข้าพัก</label>

                                <select id="cus_id" name="cus_id" class="form-select w-100">
                                    <option value="">-- เลือกผู้เข้าพัก --</option>

                                    <?php while ($customer = $resultCustomers->fetch_assoc()) { ?>
                                    <option value="<?= $customer['cus_id'] ?>">
                                        <?= htmlspecialchars($customer['cus_name']) ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            ยกเลิก</button><button class="btn btn-primary px-3">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="/admin/rooms.js"></script>
    <script>
        function openEditModal(button) {
            const form = document.getElementById('roomForm2');
            form.elements.room_id.value = button.dataset.roomId;
            form.elements.room_number.value = button.dataset.roomNumber;
            form.elements.room_type.value = button.dataset.roomType;
            form.elements.price.value = button.dataset.roomPrice;
            form.elements.status.value = button.dataset.roomStatus;
            form.querySelector('.modal-title').textContent = 'แก้ไขห้อง ' + button.dataset.roomNumber;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('roomModal2')).show();
        }
    </script>
</body>

</html>
