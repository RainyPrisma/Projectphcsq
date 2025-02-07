<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่ยนรหัสผ่าน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../Assets/CSS/change_password.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card glass-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-key ocean-icon"></i>
                            <h3 class="card-title mt-3">เปลี่ยนรหัสผ่าน</h3>
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
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text ocean-input-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control ocean-input" name="old_password" required placeholder="รหัสผ่านเก่า">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text ocean-input-icon">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="password" class="form-control ocean-input" name="password" required placeholder="รหัสผ่านใหม่">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="input-group">
                                    <span class="input-group-text ocean-input-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                    <input type="password" class="form-control ocean-input" name="confirm_password" required placeholder="ยืนยันรหัสผ่านใหม่">
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-ocean btn-lg">
                                    <i class="fas fa-sync-alt me-2"></i>อัปเดตรหัสผ่าน
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../Assets/JS/change_password.js"></script>
</body>
</html>