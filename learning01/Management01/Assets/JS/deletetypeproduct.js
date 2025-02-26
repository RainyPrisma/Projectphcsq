    function deleteProductType(typeId) {
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
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('เกิดข้อผิดพลาดในการลบประเภทสินค้า');
            });
        }
    }

    // จัดการการแสดงผลราคา
    document.getElementById("priceInput").addEventListener("input", function (e) {
        let value = e.target.value.replace(/[^0-9]/g, "").replace(/^0+/, ""); 
        if (value !== "") {
            e.target.value = Number(value).toLocaleString();
        } else {
            e.target.value = "";
        }
    });
    