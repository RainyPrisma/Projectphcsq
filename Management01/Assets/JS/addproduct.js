// Object สำหรับจัดการการป้องกันการกดซ้ำของฟอร์ม
const formProtection = {
    submitting: {},
    
    // ฟังก์ชันป้องกันการกดซ้ำ
    protect: function(formId, button, loadingText = 'กำลังดำเนินการ...') {
        if (this.submitting[formId]) {
            return false;
        }
        
        this.submitting[formId] = true;
        
        // เก็บข้อความเดิมของปุ่ม
        button.dataset.originalText = button.innerHTML;
        
        // เปลี่ยนข้อความปุ่มเป็น loading
        button.disabled = true;
        button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${loadingText}`;
        
        return true;
    },
    
    // ฟังก์ชันรีเซ็ตปุ่มกลับสู่สถานะปกติ
    reset: function(formId, button) {
        this.submitting[formId] = false;
        button.disabled = false;
        button.innerHTML = button.dataset.originalText;
    }
};
document.addEventListener('DOMContentLoaded', () => {
    // จัดการฟอร์มเพิ่มสินค้า
    const addProductForm = document.querySelector('form');
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[name="add"]');
            if (!formProtection.protect('addProductForm', submitButton, 'กำลังเพิ่มสินค้า...')) {
                return;
            }

            // เก็บข้อมูลฟอร์ม
            const formData = new FormData(this);
            formData.append('add', '1'); // เพิ่ม flag add เพื่อให้ตรงกับเงื่อนไข isset($_POST['add']) ในฝั่ง PHP

            // ส่งข้อมูลด้วย fetch
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = 'management.php?success=1';
                } else {
                    throw new Error('Something went wrong');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                formProtection.reset('addProductForm', submitButton);
                alert('เกิดข้อผิดพลาดในการเพิ่มสินค้า');
            });
        });
    }
});
// ฟังก์ชันสำหรับเพิ่มประเภทสินค้าใหม่
function saveNewProduct() {
    const button = document.querySelector('.modal-footer .btn-primary');
    if (!formProtection.protect('saveProductType', button, 'กำลังบันทึก...')) {
        return;
    }

    const productTypeInput = document.getElementById('newProductType');
    const productType = productTypeInput.value.trim();
    
    if (!productType) {
        productTypeInput.classList.add('is-invalid');
        formProtection.reset('saveProductType', button);
        return;
    }

    const formData = new FormData();
    formData.append('new_product_type', '1');
    formData.append('nameType', productType);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'เกิดข้อผิดพลาดในการเพิ่มประเภทสินค้า');
            formProtection.reset('saveProductType', button);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการเพิ่มประเภทสินค้า');
        formProtection.reset('saveProductType', button);
    });
}

// ฟังก์ชันสำหรับลบประเภทสินค้า
function deleteProductType(typeId) {
    const button = document.querySelector(`button[onclick="deleteProductType(${typeId})"]`);
    if (!formProtection.protect(`deleteProductType_${typeId}`, button, 'กำลังลบ...')) {
        return;
    }

    if (confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทสินค้านี้?')) {
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'delete_product_type=1&type_id=' + typeId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'เกิดข้อผิดพลาดในการลบประเภทสินค้า');
                formProtection.reset(`deleteProductType_${typeId}`, button);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการลบประเภทสินค้า');
            formProtection.reset(`deleteProductType_${typeId}`, button);
        });
    } else {
        formProtection.reset(`deleteProductType_${typeId}`, button);
    }
}