document.addEventListener('DOMContentLoaded', function() {
    // จัดการการลบสินค้าออกจากตะกร้า
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const itemIndex = form.querySelector('input[name="item_index"]').value;

            confirmDelete(
                'ลบสินค้าออกจากตะกร้า?',
                'คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?'
            ).then((result) => {
                if (result.isConfirmed) {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'remove_item=1&item_index=' + encodeURIComponent(itemIndex)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showSuccess('ลบสำเร็จ!', data.message).then(() => {
                                location.reload();
                            });
                        } else {
                            showError('เกิดข้อผิดพลาด!', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('เกิดข้อผิดพลาด!', 'ไม่สามารถลบสินค้าได้ กรุณาลองใหม่');
                    });
                }
            });
        });
    });

    // จัดการการล้างตะกร้า
    document.querySelector('.btn-clear')?.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');

        confirmDelete(
            'ล้างตะกร้าสินค้า?',
            'คุณแน่ใจหรือไม่ว่าต้องการล้างตะกร้าทั้งหมด?'
        ).then((result) => {
            if (result.isConfirmed) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'clear_cart=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showSuccess('ล้างสำเร็จ!', data.message).then(() => {
                            location.reload();
                        });
                    } else {
                        showError('เกิดข้อผิดพลาด!', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('เกิดข้อผิดพลาด!', 'ไม่สามารถล้างตะกร้าได้ กรุณาลองใหม่');
                });
            }
        });
    });

    // จัดการการชำระเงิน
    document.querySelector('.btn-checkout')?.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');

        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'checkout=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showSuccess('สั่งซื้อสำเร็จ!', data.message).then(() => {
                    location.reload();
                });
            } else {
                showError('เกิดข้อผิดพลาด!', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('เกิดข้อผิดพลาด!', 'ไม่สามารถดำเนินการชำระเงินได้ กรุณาลองใหม่');
        });
    });
});