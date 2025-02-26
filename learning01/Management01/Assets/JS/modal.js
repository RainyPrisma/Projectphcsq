function showModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('customModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideModal() {
    document.getElementById('customModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ปิด modal เมื่อคลิกพื้นหลัง
document.getElementById('customModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideModal();
    }
});