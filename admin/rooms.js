function openAdd() {
      roomForm.reset();
      editId.value = "";
      modalTitle.textContent = "เพิ่มห้องพัก";
    }

 function editRoom(id) {
      const r = rooms.find((x) => x.id === id);
      editId.value = id;
      number.value = r.number;
      type.value = r.type;
      floor.value = r.floor;
      price.value = r.price;
      status.value = r.status;
      guest.value = r.guest;
      modalTitle.textContent = "แก้ไขห้อง " + r.number;
      bootstrap.Modal.getOrCreateInstance("#roomModal").show();
    }
    function deleteRoom(id) {
      const r = rooms.find((x) => x.id === id);
      if (r.status === "ไม่ว่าง") {
        alert(
          "ไม่สามารถลบห้องที่มีผู้เข้าพักได้ กรุณาเช็กเอาต์หรือเปลี่ยนเป็น “ปิดใช้งาน” ก่อน",
        );
        return;
      }
      if (confirm(`ต้องการลบห้อง ${r.number} ใช่หรือไม่?`)) {
        rooms = rooms.filter((x) => x.id !== id);
        render();
      }
    }