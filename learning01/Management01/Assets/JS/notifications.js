// ../Assets/JS/notifications.js
function refreshNotifications() {
    fetch('../Notification/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // อัพเดทจำนวนแจ้งเตือนที่ยังไม่ได้อ่าน
            const unreadCountElement = document.getElementById('unread_count');
            if (unreadCountElement) {
                unreadCountElement.innerText = data.unread_count;
            } else {
                console.error('Element with id "unread_count" not found');
            }

            // อัพเดทรายการแจ้งเตือน
            const notificationList = document.getElementById('notification_list');
            if (notificationList) {
                notificationList.innerHTML = ''; // ล้างข้อมูลเก่า

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        const item = `
                            <li>
                                <a class="dropdown-item d-flex align-items-center ${notification.is_read ? 'text-muted' : ''}" 
                                   href="mark_as_read.php?id=${notification.id}">
                                    <div class="flex-shrink-0">
                                        <i class="bi ${
                                            notification.type === 'order' ? 'bi-box-seam text-primary' :
                                            notification.type === 'promotion' ? 'bi-percent text-success' :
                                            notification.type === 'product' ? 'bi-tag-fill text-warning' :
                                            'bi-info-circle text-info'
                                        } fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">${notification.title}</p>
                                        <p class="text-muted small mb-0">${notification.message}</p>
                                        <p class="text-muted small mb-0">${notification.created_at}</p>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                        `;
                        notificationList.insertAdjacentHTML('beforeend', item);
                    });
                } else {
                    notificationList.innerHTML = `
                        <li><a class="dropdown-item text-center text-muted" href="notifications.php">ไม่มีแจ้งเตือนใหม่</a></li>
                        <li><hr class="dropdown-divider"></li>
                    `;
                }
            } else {
                console.error('Element with id "notification_list" not found');
            }
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// รอให้ DOM โหลดเสร็จก่อนรัน
document.addEventListener('DOMContentLoaded', () => {
    // รีเฟรชครั้งแรกเมื่อโหลดหน้า
    refreshNotifications();

    // รีเฟรชทุก 60 วินาที
    setInterval(refreshNotifications, 10000);
});