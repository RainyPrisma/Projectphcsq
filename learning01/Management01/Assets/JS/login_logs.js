document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const resetBtn = document.getElementById('reset-btn');
    const dataContainer = document.getElementById('data-container');
    const paginationContainer = document.getElementById('pagination-container');
    
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

    function setFiltersFromResponse(filters) {
        for (const key in filters) {
            const input = filterForm.elements[key];
            if (input && filters[key]) {
                input.value = filters[key];
            }
        }
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
                    <td>${formatIPAddress(row.ip_address)}</td>
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

    function renderPagination(pagination) {
        if (!pagination || pagination.total_pages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '<nav><ul class="pagination justify-content-center">';
        
        // ปุ่ม Previous
        html += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;
        
        // สร้างปุ่มตัวเลขหน้า
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (
                i === 1 || // หน้าแรก
                i === pagination.total_pages || // หน้าสุดท้าย
                Math.abs(i - pagination.current_page) <= 2 // หน้าใกล้เคียงปัจจุบัน
            ) {
                html += `
                    <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (
                i === pagination.current_page - 3 ||
                i === pagination.current_page + 3
            ) {
                html += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
        }
        
        // ปุ่ม Next
        html += `
            <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;
        
        html += '</ul></nav>';
        
        paginationContainer.innerHTML = html;
        
        // เพิ่ม event listener สำหรับปุ่มเปลี่ยนหน้า
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (!isNaN(page) && page > 0) {
                    loadData(page);
                }
            });
        });
    }
    
    function renderStatus(isActive) {
        if (isActive == 1) {
            return '<span class="badge bg-success">กำลังใช้งาน</span>';
        } else {
            return '<span class="badge bg-secondary">ออกจากระบบแล้ว</span>';
        }
    }
    
    function formatIPAddress(ip) {
        if (!ip) return '<span class="text-muted">-</span>';
        
        // จัดการกรณีพิเศษ
        if (ip === 'localhost' || ip === 'Unknown') {
            return `<span class="badge bg-secondary">${escapeHtml(ip)}</span>`;
        }

        // ตรวจสอบว่าเป็น IP ที่ถูกต้องหรือไม่
        const ipPattern = /^(\d{1,3}\.){3}\d{1,3}$|^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/;
        if (ipPattern.test(ip)) {
            return `<code>${escapeHtml(ip)}</code>`;
        }
        
        // กรณีที่ไม่ใช่รูปแบบ IP ที่ถูกต้อง
        return `<span class="text-muted">${escapeHtml(ip)}</span>`;
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
    
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function loadInitialFiltersFromUrl() {
        const url = new URL(window.location.href);
        const params = url.searchParams;
        
        // ดึงค่าจาก URL parameters มาใส่ในฟอร์ม
        const fields = ['username', 'email', 'is_active', 'date_from', 'date_to'];
        
        fields.forEach(field => {
            const value = params.get(field);
            if (value) {
                const input = filterForm.elements[field];
                if (input) {
                    input.value = value;
                }
            }
        });
        
        // ถ้ามีการกำหนด page ใน URL ให้โหลดข้อมูลหน้านั้น
        const page = params.get('page');
        if (page) {
            loadData(parseInt(page));
        }
    }

    // เริ่มต้นโหลดข้อมูล
    loadInitialFiltersFromUrl();
    loadData();
});