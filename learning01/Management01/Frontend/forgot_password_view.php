<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../Assets/CSS/change_password.css" rel="stylesheet">
    <!-- เพิ่ม SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card glass-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-key ocean-icon"></i>
                            <h3 class="card-title mt-3">รีเซ็ตรหัสผ่าน</h3>
                            <p class="text-muted">กรุณากรอกอีเมลเพื่อรับลิงก์รีเซ็ตรหัสผ่าน</p>
                        </div>
                        
                        <form id="changePasswordForm">
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text ocean-input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control ocean-input" name="email" required placeholder="อีเมลของคุณ">
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-ocean btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>ส่งลิงก์รีเซ็ต
                                </button>
                            </div>
                        </form>

                        <div id="successMessage" class="alert alert-success mt-3 d-none">
                            <i class="fas fa-check-circle me-2"></i>ส่งลิงก์รีเซ็ตรหัสผ่านไปที่อีเมลของคุณเรียบร้อยแล้ว
                        </div>
                        <div id="errorMessage" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const email = this.querySelector('input[name="email"]').value;
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            try {
                const response = await fetch('/learning01/Management01/Backend/forgot_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ email })
                });
                
                const text = await response.text();
                console.log('Raw response:', text);
                const data = JSON.parse(text);

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ!',
                        text: 'ส่งลิงก์รีเซ็ตไปที่อีเมลเรียบร้อยแล้ว',
                        confirmButtonText: 'ตกลง',
                        confirmButtonColor: '#3085d6'
                    });
                    successMsg.classList.add('d-none');
                    errorMsg.classList.add('d-none');
                    this.reset();
                } else {
                    errorMsg.textContent = data.message;
                    errorMsg.classList.remove('d-none');
                    successMsg.classList.add('d-none');
                }
            } catch (error) {
                errorMsg.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message;
                errorMsg.classList.remove('d-none');
                successMsg.classList.add('d-none');
            }
        });
    </script>
</body>
</html>