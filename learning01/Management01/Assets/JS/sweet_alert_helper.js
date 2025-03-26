// ฟังก์ชันสำหรับแสดงกล่องยืนยันการลบ
function confirmDelete(title = 'คุณแน่ใจหรือไม่?', text = 'การกระทำนี้ไม่สามารถย้อนกลับได้', confirmText = 'ใช่, ลบเลย!', cancelText = 'ยกเลิก') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}

// ฟังก์ชันสำหรับแสดงข้อความสำเร็จ
function showSuccess(title = 'สำเร็จ!', text = 'การดำเนินการเสร็จสิ้นเรียบร้อยแล้ว') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        confirmButtonColor: '#3085d6'
    });
}

// ฟังก์ชันสำหรับแสดงข้อความข้อผิดพลาด
function showError(title = 'เกิดข้อผิดพลาด!', text = 'กรุณาลองใหม่ภายหลัง') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        confirmButtonColor: '#d33'
    });
}