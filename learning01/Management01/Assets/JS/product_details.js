// จำกัดจำนวนไฟล์ที่อัปโหลด
function limitFiles(input, max) {
    if (input.files.length > max) {
        alert("คุณสามารถอัปโหลดได้สูงสุด " + max + " รูปเท่านั้น");
        input.value = "";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // เปลี่ยนรูปภาพหลักเมื่อคลิก thumbnail
    document.querySelectorAll('.thumbnail').forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            document.getElementById('main-product-image').src = this.getAttribute('data-image');
        });
    });

    // จัดการการลบรูปภาพเมื่อคลิกปุ่มกากบาท (สำหรับ admin เท่านั้น)
    if (typeof isAdmin !== 'undefined' && isAdmin) {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                // เรียกใช้ confirmDelete จาก sweet_alert_helper.js
                confirmDelete(
                    'คุณแน่ใจหรือไม่?',
                    'คุณต้องการลบรูปภาพนี้จริง ๆ ใช่ไหม? การกระทำนี้ไม่สามารถย้อนกลับได้',
                    'ใช่, ลบเลย!',
                    'ยกเลิก'
                ).then((result) => {
                    if (result.isConfirmed) {
                        // ส่งคำขอลบผ่าน AJAX
                        fetch(window.location.href, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'delete_image=1&image_index=' + encodeURIComponent(index)
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log('Delete response:', data);
                            // เรียกใช้ showSuccess จาก sweet_alert_helper.js
                            showSuccess('ลบสำเร็จ!', 'รูปภาพถูกลบเรียบร้อยแล้ว').then(() => {
                                location.reload(); // รีเฟรชหน้าเพื่ออัปเดตข้อมูล
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // เรียกใช้ showError จาก sweet_alert_helper.js
                            showError('เกิดข้อผิดพลาด!', 'ไม่สามารถลบรูปภาพได้ กรุณาลองใหม่');
                        });
                    }
                });
            });
        });
    }

    // ตรวจสอบว่าเป็น admin และมี element sortable-thumbnails หรือไม่
    if (typeof isAdmin !== 'undefined' && isAdmin && document.getElementById('sortable-thumbnails')) {
        const sortableThumbnails = document.getElementById('sortable-thumbnails');
        new Sortable(sortableThumbnails, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const thumbnails = Array.from(sortableThumbnails.querySelectorAll('.thumbnail:not(.active)'));
                const newOrder = thumbnails.map(thumbnail => parseInt(thumbnail.getAttribute('data-index')));

                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'sort_images=' + encodeURIComponent(JSON.stringify(newOrder))
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Sort saved:', data);
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
});