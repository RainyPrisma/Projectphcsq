document.addEventListener('DOMContentLoaded', function() {
    // Modal handling
    const modalElement = document.getElementById('addProductModal');    
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function () {
            const newTypeInput = document.getElementById('newProductType');
            if (newTypeInput) {
                newTypeInput.value = '';
                newTypeInput.classList.remove('is-invalid');
            }
        });

        // เพิ่ม event listener สำหรับ modal show
        modalElement.addEventListener('shown.bs.modal', function () {
            const newTypeInput = document.getElementById('newProductType');
            if (newTypeInput) {
                newTypeInput.focus();
            }
        });
    }

    // Price input handling
    const priceInput = document.getElementById("priceInput");
    if (priceInput) {
        priceInput.addEventListener("input", function(e) {
            let value = e.target.value.replace(/[^0-9]/g, "").replace(/^0+/, ""); 
            if (value !== "") {
                e.target.value = Number(value).toLocaleString();
            } else {
                e.target.value = "";
            }
        });
    }
});

function saveNewProduct() {
    const newTypeInput = document.getElementById('newProductType');
    if (!newTypeInput) return;

    const newType = newTypeInput.value.trim();
    
    // ตรวจสอบว่ามีการกรอกข้อมูล
    if (!newType) {
        newTypeInput.classList.add('is-invalid');
        newTypeInput.focus();
        return;
    }

    newTypeInput.classList.remove('is-invalid');
    
    // แสดง loading state
    const saveButton = document.querySelector('#addProductModal .btn-primary');
    if (!saveButton) return;

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
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // เพิ่มตัวเลือกใหม่ใน select
            const select = document.getElementById('product_id');
            if (select) {
                const option = new Option(newType, data.id);
                select.add(option);
                select.value = data.id;
            }
            
            // ปิด modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
            if (modal) {
                modal.hide();
                newTypeInput.value = '';
                newTypeInput.classList.remove('is-invalid');
            }
            
            // แสดงข้อความสำเร็จ
            showAlert('success', 'เพิ่มประเภทสินค้าสำเร็จ');
        } else {
            showAlert('danger', data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    })
    .finally(() => {
        if (saveButton) {
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
        }
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '1050';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
