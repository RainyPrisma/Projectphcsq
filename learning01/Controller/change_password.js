document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังดำเนินการ...';
    submitButton.disabled = true;
    
    const formData = new FormData(this);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        showAlert(data.message, data.status);
        if (data.status === 'success') {
            setTimeout(() => {
                window.location.href = '../login.php';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('เกิดข้อผิดพลาด! กรุณาลองใหม่', 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});

function showAlert(message, status) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${status === 'success' ? 'success' : 'danger'} alert-float`;
    alertDiv.innerHTML = `<i class="fas fa-${status === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}