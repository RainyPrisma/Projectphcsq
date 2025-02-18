document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const resetBtn = document.getElementById('reset-btn');
    const dataContainer = document.getElementById('data-container');
    const paginationContainer = document.getElementById('pagination-container');
    
    // โหลดข้อมูลเริ่มต้น
    loadData();
    
    // จัดการการส่งฟอร์ม
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        loadData(1);
    });
    
    // จัดการการรีเซ็ตฟอร์ม
    resetBtn.addEventListener('click', function() {
        filterForm.reset();
        loadData(1);
    });
    
    function loadData(page = 1) {
        // แสดงตัวโหลด
        dataContainer.innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </div>
        `;
        
        const formData = new FormData(filterForm);
        formData.append('page', page);
        
        const params = new URLSearchParams();
        for (const pair of formData.entries()) {
            if (pair[1]) {
                params.append(pair[0], pair[1]);
            }
        }
        
        fetch(`get_login_logs.php?${params.toString()}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('เกิดข้อผิดพลาดในการเรียกข้อมูล');
                }
                return response.json();
            })
            .then(data => {
                renderTable(data.data);
                renderPagination(data.pagination);
                setFiltersFromResponse(data.filters);
                
                const url = new URL(window.location.href);
                for (const key in data.filters) {
                    if (data.filters[key]) {
                        url.searchParams.set(key, data.filters[key]);
                    } else {
                        url.searchParams.delete(key);
                    }
                }
                url.searchParams.set('page', data.pagination.current_page);
                window.history.replaceState({}, '', url);
            })
            .catch(error => {
                dataContainer.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        ${error.message}
                    </div>
                `;
                console.error('Error:', error);
            });
    }
    
    function renderTable(data) {
        if (data.length === 0) {
            dataContainer.innerHTML = `
                <div class="alert alert-info" role="alert">
                    ไม่พบข้อมูลที่ตรงกับเงื่อนไขการค้นหา
                </div>
            `;
            return;
        }
        
        let tableHtml = `
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ชื่อผู้ใช้</th>
                            <th>อีเมล</th>
                            <th>สถานะ</th>
                            <th>เวลาเข้าสู่ระบบ</th>
                            <th>เวลาออกจากระบบ</th>
                            <th>ระยะเวลาใช้งาน</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        data.forEach(row => {
            const loginTime = new Date(row.login_time);
            const logoutTime = row.logout_time ? new Date(row.logout_time) : null;
            const duration = calculateDuration(loginTime, logoutTime);
            
            tableHtml += `
                <tr>
                    <td>${escapeHtml(row.username)}</td>
                    <td>${escapeHtml(row.email)}</td>
                    <td>${renderStatus(row.is_active)}</td>
                    <td>${formatDateTime(loginTime)}</td>
                    <td>${logoutTime ? formatDateTime(logoutTime) : '<span class="text-muted">-</span>'}</td>
                    <td>${duration}</td>
                    <td><code>${formatIPAddress(row.ip_address)}</code></td>
                </tr>
            `;
        });
        
        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;
        
        dataContainer.innerHTML = tableHtml;
    }
    
    function renderStatus(isActive) {
        if (isActive == 1) {
            return '<span class="badge bg-success">กำลังใช้งาน</span>';
        } else {
            return '<span class="badge bg-secondary">ออกจากระบบแล้ว</span>';
        }
    }
    
    function formatIPAddress(ip) {
        return ip.replace(/[^0-9.]/g, ''); // ลบอักขระที่ไม่ใช่ตัวเลขและจุด
    }
    
    function calculateDuration(loginTime, logoutTime) {
        if (!logoutTime) {
            return '<span class="text-muted">-</span>';
        }
        
        const diff = logoutTime - loginTime;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        
        if (hours > 0) {
            return `${hours} ชั่วโมง ${remainingMinutes} นาที`;
        } else {
            return `${minutes} นาที`;
        }
    }
    
    function formatDateTime(date) {
        return date.toLocaleString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });
    }
    
    // ฟังก์ชันที่มีอยู่เดิมยังคงเหมือนเดิม...
    function renderPagination(pagination) {
        // ... (คงเดิม)
    }
    
    function setFiltersFromResponse(filters) {
        // ... (คงเดิม)
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    loadInitialFiltersFromUrl();
});