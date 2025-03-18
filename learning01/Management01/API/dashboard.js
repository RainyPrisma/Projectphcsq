function fetchDashboardData() {
    fetch('../API/dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            // ตรวจสอบและอัปเดตข้อมูล
            const username = document.getElementById('username');
            if (username) username.textContent = data.username;

            const lastLogin = document.getElementById('last_login');
            if (lastLogin) lastLogin.textContent = data.last_login;

            const totalOrders = document.getElementById('total_orders');
            if (totalOrders) totalOrders.textContent = `${data.total_orders} ครั้ง`;

            const totalOrdersProgress = document.getElementById('total_orders_progress');
            if (totalOrdersProgress) totalOrdersProgress.style.width = `${Math.min(100, data.total_orders * 5)}%`;

            const totalSpending = document.getElementById('total_spending');
            if (totalSpending) totalSpending.textContent = `฿${data.total_spending}`;

            const totalSpendingProgress = document.getElementById('total_spending_progress');
            if (totalSpendingProgress) totalSpendingProgress.style.width = `${Math.min(100, data.total_spending / 100)}%`;

            const mostPurchasedItem = document.getElementById('most_purchased_item');
            if (mostPurchasedItem) mostPurchasedItem.textContent = data.most_purchased_item;

            // อัปเดตการแจ้งเตือน
            const unreadCount = document.getElementById('unread_count');
            if (unreadCount) unreadCount.textContent = data.unread_count;

            const notificationList = document.getElementById('notification_list');
            if (notificationList) {
                notificationList.innerHTML = '';
                if (data.notifications.length > 0) {
                    data.notifications.forEach(notif => {
                        const item = `
                            <li>
                                <a class="dropdown-item d-flex align-items-center ${notif.is_read ? 'text-muted' : ''}" 
                                   href="mark_as_read.php?id=${notif.id}">
                                    <div class="flex-shrink-0">
                                        <i class="bi ${notif.type === 'order' ? 'bi-box-seam text-primary' : 
                                                    notif.type === 'promotion' ? 'bi-percent text-success' : 
                                                    notif.type === 'product' ? 'bi-tag-fill text-warning' : 
                                                    'bi-info-circle text-info'} fs-4"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="mb-0 fw-bold">${notif.title}</p>
                                        <p class="text-muted small mb-0">${notif.message}</p>
                                        <p class="text-muted small mb-0">${new Date(notif.created_at).toLocaleString('th-TH', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })} น.</p>
                                    </div>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>`;
                        notificationList.innerHTML += item;
                    });
                } else {
                    notificationList.innerHTML = '<li><a class="dropdown-item text-center text-muted" href="../Notification/notification.php">ไม่มีแจ้งเตือนใหม่</a></li><li><hr class="dropdown-divider"></li>';
                }
            }

            // อัปเดตคำสั่งซื้อล่าสุด
            const latestOrderContainer = document.getElementById('latest_order_container');
            if (latestOrderContainer) {
                if (data.latest_order) {
                    latestOrderContainer.innerHTML = `
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary p-2">คำสั่งซื้อ #${data.latest_order.order_reference}</span>
                            <span class="fw-bold fs-5">฿${data.latest_order.total_price}</span>
                        </div>
                        <div class="mb-3 p-3 border rounded bg-light">
                            <div class="mb-2">
                                <i class="bi bi-clock me-1 text-secondary"></i>
                                <span class="text-muted">วันที่สั่งซื้อ:</span>
                                <span class="fw-medium ms-2">${data.latest_order.created_at}</span>
                            </div>
                            <div class="mb-2">
                                <i class="bi bi-geo-alt me-1 text-secondary"></i>
                                <span class="text-muted">วิธีจัดส่ง:</span>
                                <span class="fw-medium ms-2">Kerry Express (ด่วนพิเศษ)</span>
                            </div>
                            <div>
                                <i class="bi bi-credit-card me-1 text-secondary"></i>
                                <span class="text-muted">วิธีชำระเงิน:</span>
                                <span class="fw-medium ms-2">บัตรเครดิต</span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="../Users/ordercus_history.php" class="btn btn-ocean">
                                <i class="bi bi-eye me-1"></i> ดูรายละเอียด
                            </a>
                        </div>`;
                } else {
                    latestOrderContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
                            <p class="text-muted">ยังไม่มีคำสั่งซื้อ</p>
                            <a href="../Product/gallery.php" class="btn btn-ocean">
                                <i class="bi bi-cart-plus me-1"></i> เริ่มการช้อปปิ้ง
                            </a>
                        </div>`;
                }
            }
        })
        .catch(error => console.error('Error fetching dashboard data:', error));
}

document.addEventListener('DOMContentLoaded', fetchDashboardData);