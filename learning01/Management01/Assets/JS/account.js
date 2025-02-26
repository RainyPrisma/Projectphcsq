document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('accountForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // แสดง loading indicator
        const submitButton = form.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = 'Saving...';
        submitButton.disabled = true;
        
        // รวบรวมข้อมูลจากฟอร์ม
        const formData = new FormData(form);
        
        // ส่งข้อมูลด้วย AJAX
        fetch('../Database/account_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('success', 'บันทึกข้อมูลสำเร็จ');
            } else {
                showNotification('error', data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }
        })
        .catch(error => {
            showNotification('error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
            console.error('Error:', error);
        })
        .finally(() => {
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        });
    });
});

function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 25px';
    notification.style.borderRadius = '5px';
    notification.style.color = '#fff';
    notification.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    notification.style.zIndex = '1000';
    notification.style.opacity = '0';
    notification.style.transition = 'opacity 0.3s ease-in-out';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 100);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}