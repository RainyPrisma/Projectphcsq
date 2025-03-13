document.addEventListener('DOMContentLoaded', () => {
    // Function to fetch data via AJAX
    function fetchData(url, callback) {
        fetch(url)
            .then(response => response.json())
            .then(data => callback(data))
            .catch(error => console.error('Error:', error));
    }

    // Helper function to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#x27;");
    }

    // Load Dashboard Stats
    fetchData('../Database/api.php?action=dashboard_stats', (data) => {
        if (data.error) {
            alert(data.error);
            window.location.href = '../Frontend/index.php';
            return;
        }
        document.getElementById('total-users').textContent = data.total_users;
        document.getElementById('total-orders').textContent = data.total_orders;
        document.getElementById('total-products').textContent = data.total_products;
        document.getElementById('total-revenue').textContent = '$' + Number(data.total_revenue).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    });

    // Load Recent Logins
    fetchData('../Database/api.php?action=recent_logins', (data) => {
        const tbody = document.getElementById('recent-logins');
        tbody.innerHTML = '';
        data.forEach(login => {
            const row = `<tr>
                <td>${escapeHtml(login.username)}</td>
                <td>${escapeHtml(login.email)}</td>
                <td>${login.login_time}</td>
                <td>${escapeHtml(login.ip_address)}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    });

    // Load Product Categories
    fetchData('../Database/api.php?action=product_categories', (data) => {
        const tbody = document.getElementById('product-categories');
        tbody.innerHTML = '';
        data.forEach(category => {
            const row = `<tr>
                <td>${escapeHtml(category.nameType)}</td>
                <td>${category.count}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    });
});