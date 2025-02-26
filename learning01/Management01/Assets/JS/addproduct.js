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

// Event Listener เมื่อ DOM โหลดเสร็จ
document.addEventListener('DOMContentLoaded', () => {
    // จัดการ input event สำหรับ newProductType
    const productTypeInput = document.getElementById('newProductType');
    if (productTypeInput) {
        productTypeInput.addEventListener('input', () => {
            productTypeInput.classList.remove('is-invalid');
        });
    }

    // จัดการฟอร์มเพิ่มสินค้า
    const addProductForm = document.querySelector('form#myForm');
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = this.querySelector('button[name="add"]');
            if (!formProtection.protect('addProductForm', submitButton, 'กำลังเพิ่มสินค้า...')) {
                return;
            }

            // เก็บข้อมูลฟอร์ม
            const formData = new FormData(this);
            formData.append('add', '1');

            // ส่งข้อมูลด้วย fetch
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                window.location.href = 'management.php?success=1';
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
    const button = document.querySelector('#addProductModal .modal-footer .btn-primary');
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

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            productTypeInput.value = '';
            productTypeInput.classList.remove('is-invalid');
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            if (modal) {
                modal.hide();
            }
            location.reload();
        } else {
            throw new Error(data.message || 'เกิดข้อผิดพลาดในการเพิ่มประเภทสินค้า');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'เกิดข้อผิดพลาดในการเพิ่มประเภทสินค้า');
        formProtection.reset('saveProductType', button);
    });
}

// ฟังก์ชันสำหรับลบประเภทสินค้า
function deleteProductType(typeId) {
    const button = document.querySelector(`button[onclick="deleteProductType(${typeId})"]`);
    if (!formProtection.protect(`deleteProductType_${typeId}`, button, 'กำลังลบ...')) {
        return;
    }

    if (!confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทสินค้านี้?')) {
        formProtection.reset(`deleteProductType_${typeId}`, button);
        return;
    }

    const formData = new URLSearchParams();
    formData.append('delete_product_type', '1');
    formData.append('type_id', typeId);

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบประเภทสินค้า');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'เกิดข้อผิดพลาดในการลบประเภทสินค้า');
        formProtection.reset(`deleteProductType_${typeId}`, button);
    });
}