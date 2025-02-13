function saveNewProduct() {
    const newTypeInput = document.getElementById('newProductType');
    const newType = newTypeInput.value.trim();
    
    // ตรวจสอบว่ามีการกรอกข้อมูล
    if (!newType) {
        newTypeInput.classList.add('is-invalid');
        return;
    }
    newTypeInput.classList.remove('is-invalid');
    
    // แสดง loading state
    const saveButton = document.querySelector('#addProductModal .btn-primary');
    const originalText = saveButton.innerHTML;
    saveButton.disabled = true;
    saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> กำลังบันทึก...';
    
    // ส่งข้อมูลไปบันทึก
    fetch('save_product_type.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'nameType=' + encodeURIComponent(newType)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // เพิ่มตัวเลือกใหม่ใน select
            const select = document.getElementById('product_id');
            const option = new Option(newType, data.id);
            select.add(option);
            select.value = data.id;
            
            // ปิด modal และรีเซ็ตฟอร์ม
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            modal.hide();
            newTypeInput.value = '';
            
            // ลบ backdrop และคืนค่า body
            document.querySelector('.modal-backdrop')?.remove();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
            
            // แสดงข้อความสำเร็จ
            showAlert('success', 'เพิ่มประเภทสินค้าสำเร็จ');
        } else {
            showAlert('danger', 'เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        showAlert('danger', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    })
    .finally(() => {
        // คืนค่าปุ่มบันทึกให้กลับสู่สถานะปกติ
        saveButton.disabled = false;
        saveButton.innerHTML = originalText;
    });
}

// ฟังก์ชันแสดงข้อความแจ้งเตือน
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // แทรกการแจ้งเตือนที่ด้านบนของหน้า
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    // ลบการแจ้งเตือนอัตโนมัติหลัง 3 วินาที
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// เพิ่ม event listener เมื่อ modal ถูกปิด
document.getElementById('addProductModal').addEventListener('hidden.bs.modal', function () {
    // ลบ backdrop และคืนค่า body
    document.querySelector('.modal-backdrop')?.remove();
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
});