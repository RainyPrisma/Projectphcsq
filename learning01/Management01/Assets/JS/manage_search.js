document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const tbody = document.querySelector('tbody');
    const errorContainer = document.createElement('div');
    errorContainer.className = 'alert alert-danger d-none';
    searchInput.parentNode.insertBefore(errorContainer, searchInput.nextSibling);
    
    let searchTimeout;
    // Initial search สำหรับค้นห่าข้อมูลทั้งหมดโดยเก็บจากตัวแปรที่ถูกส่งมาจาก Backend
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(this.value);
        }, 300);
    });

    function performSearch(searchTerm) {
        errorContainer.classList.add('d-none');
        
        fetch(`../Backend/search_handler.php?search=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    throw new Error(data.message);
                }
                updateTable(data.data);
            })
            .catch(error => {
                console.error('Error:', error);
                errorContainer.textContent = `เกิดข้อผิดพลาด: ${error.message}`;
                errorContainer.classList.remove('d-none');
            });
    }

    function updateTable(products) {
        tbody.innerHTML = ''; // Clear existing rows
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">ไม่พบข้อมูลที่ค้นหา</td>
                </tr>
            `;
            return;
        }

        products.forEach(product => {
            tbody.innerHTML += generateTableRow(product);
        });
    }
    // สร้างตารางข้อมูลสินค้า
    function generateTableRow(product) {
        return `
            <tr>
                <td>${product.id}</td>
                <td>${product.product_id}</td>
                <td>${product.name}</td>
                <td>${product.detail}</td>
                <td>${product.price} บาท</td>
                <td>${product.quantity}</td>
                <td>${product.orderdate}</td>
                <td><img src="${product.image_url}" alt="${product.name}" style="max-width: 100px;"></td>
                <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal${product.id}">แก้ไข</button>
                    <a href="?delete=${product.id}" class="btn btn-danger btn-sm" onclick="return confirm('ยืนยันการลบ?')">ลบ</a>
                </td>
            </tr>
            
            <!-- Modal แก้ไขสินค้า -->
            ${generateEditModal(product)}
        `;
    }
    // สร้าง Modal แก้ไขข้อมูลสินค้า
    function generateEditModal(product) {
        return `
            <div class="modal fade" id="editModal${product.id}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">แก้ไขข้อมูลสินค้า</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post">
                            <div class="modal-body">
                                <input type="hidden" name="id" value="${product.id}">
                                <input type="hidden" name="product_id" value="${product.product_id}">
                                <div class="mb-3">
                                    <label class="form-label">ชื่อสินค้า</label>
                                    <input type="text" class="form-control" name="name" value="${product.name}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">รายละเอียด</label>
                                    <textarea class="form-control" name="detail" required>${product.detail}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ราคา</label>
                                    <input type="number" class="form-control" name="price" value="${parseFloat(product.price.replace(/,/g, ''))}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">จำนวน</label>
                                    <input type="number" class="form-control" name="quantity" value="${product.quantity}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">วันที่สั่ง</label>
                                    <input type="datetime-local" class="form-control" name="orderdate" value="${product.orderdate.replace(' ', 'T')}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">URL รูปภาพ</label>
                                    <input type="text" class="form-control" name="image_url" value="${product.image_url}" required>
                                    <div class="mt-2">
                                        <img src="${product.image_url}" alt="Preview" style="max-width: 100px;">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                <button type="submit" name="update" class="btn btn-primary">บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }
});