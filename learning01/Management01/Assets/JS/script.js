document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownContent = document.querySelector('.dropdown-content');
    
    dropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (dropdownContent.classList.contains('active')) {
            // ถ้าเปิดอยู่แล้ว ให้ปิดและรีเซ็ต animation
            dropdownContent.classList.remove('active');
            // รอสักครู่แล้วค่อยเปิดใหม่เพื่อให้ animation ทำงานซ้ำ
            setTimeout(() => {
                dropdownContent.classList.add('active');
            }, 10);
        } else {
            // ถ้ายังไม่เปิด ให้เปิดปกติ
            dropdownContent.classList.add('active');
        }
    });

    // ส่วนที่เหลือคงเดิม
    document.addEventListener('click', (e) => {
        if (!dropdownContent.contains(e.target) && !dropdownBtn.contains(e.target)) {
            dropdownContent.classList.remove('active');
        }
    });

    dropdownContent.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});