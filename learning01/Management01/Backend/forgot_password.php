<?php
session_start();
include '../Database/config.php';

// ตั้งค่า timezone ให้ตรงกัน
date_default_timezone_set('Asia/Bangkok');
$conn->query("SET time_zone = '+07:00';");

// แก้ไขเส้นทางให้ถูกต้อง (vendor อยู่ใน Backend/vendor)
if (!file_exists('vendor/autoload.php')) {
    die(json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ vendor/autoload.php ภายใน Backend/vendor']));
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ปิดการแสดง error เพื่อป้องกัน output ที่ไม่ใช่ JSON
ini_set('display_errors', 0);
error_reporting(0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่าเป็น AJAX request
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
        echo json_encode(['status' => 'error', 'message' => 'การร้องขอไม่ถูกต้อง']);
        exit();
    }

    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    // ถ้ามีแค่ email แปลว่าขอลิงก์รีเซ็ต
    if (isset($data['email']) && !isset($data['token'])) {
        $email = $data['email'];

        try {
            // ตรวจสอบว่ามีอีเมลนี้ในระบบหรือไม่ และดึง username
            $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                throw new Exception('ไม่พบอีเมลนี้ในระบบ!');
            }

            // ดึง username และเก็บใน session
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $_SESSION['reset_username'] = $username; // เก็บ username ใน session ชั่วคราว

            // สร้าง token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // อัปเดต token ในตาราง users
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expires_at = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expires, $email);
            $stmt->execute();

            if ($stmt->affected_rows == 0) {
                throw new Exception('เกิดข้อผิดพลาดในการบันทึก token! Email: ' . $email);
            }

            // Debug: ตรวจสอบว่า token ถูกบันทึกจริง
            $stmt = $conn->prepare("SELECT reset_token, token_expires_at FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            error_log("Token saved: " . $row['reset_token'] . ", Expires: " . $row['token_expires_at']);

            // ส่งอีเมล
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPDebug = 2; // เปิด debug mode
            $mail->Debugoutput = function($str, $level) {
                error_log("PHPMailer Debug [$level]: $str\n", 3, 'phpmailer.log');
            };
            $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'bignagniza13@gmail.com'; // อีเมล Gmail ของคุณ
            $mail->Password = 'dugxaxfziwqizhpk'; // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('bignagniza13@gmail.com', 'AdminSeafoodHub');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Resetpassword Request';

            // ออกแบบ HTML สำหรับอีเมล
            $resetLink = "http://localhost/learning01/Management01/Backend/forgot_password.php?token=" . $token;
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="th">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Resetpassword Request</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #ffffff;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        text-align: center;
                        padding: 20px 0;
                        background-color: #007bff;
                        color: #ffffff;
                        border-radius: 8px 8px 0 0;
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 24px;
                    }
                    .content {
                        padding: 20px;
                        text-align: center;
                    }
                    .content p {
                        font-size: 16px;
                        color: #333333;
                        line-height: 1.5;
                    }
                    .button {
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #007bff;
                        color: #ffffff;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        margin: 20px 0;
                    }
                    .button:hover {
                        background-color: #0056b3;
                    }
                    .footer {
                        text-align: center;
                        padding: 10px;
                        font-size: 14px;
                        color: #777777;
                    }
                    .footer p {
                        margin: 5px 0;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>Seafood HUB</h1>
                    </div>
                    <div class="content">
                        <h2>Reset password</h2>
                        <p>สวัสดีคุณ ' . htmlspecialchars($_SESSION['reset_username']) . ',</p>
                        <p>เราได้รับคำขอให้รีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ กรุณาคลิกปุ่มด้านล่างเพื่อดำเนินการต่อ:</p>
                        <a href="' . $resetLink . '" class="button">รีเซ็ตรหัสผ่าน</a>
                        <p>ลิงก์นี้จะหมดอายุใน 1 ชั่วโมง หากคุณไม่ได้ร้องขอการรีเซ็ตนี้ กรุณาละเว้นอีเมลนี้</p>
                    </div>
                    <div class="footer">
                        <p>หากมีคำถาม กรุณาติดต่อเราที่ <a href="mailto:support@AdminSeafoodHub.com">support@AdminSeafoodHub.com</a></p>
                        <p>© ' . date('Y') . ' SeafoodHUB. สงวนลิขสิทธิ์.</p>
                    </div>
                </div>
            </body>
            </html>';

            $mail->send();

            echo json_encode(['status' => 'success', 'message' => 'ส่งลิงก์รีเซ็ตไปที่อีเมลเรียบร้อยแล้ว']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        ob_end_flush();
        exit();
    }

    // ถ้ามี token แปลว่ามาอัปเดตรหัสผ่าน
    if (isset($data['token']) && isset($data['password'])) {
        $token = $data['token'];
        $newPassword = $data['password'];
        $confirmPassword = $data['confirmPassword'] ?? $newPassword;

        try {
            // ตรวจสอบความยาวรหัสผ่าน (8 ตัวอักษรขึ้นไป)
            if (strlen($newPassword) < 8) {
                throw new Exception('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร!');
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception('รหัสผ่านใหม่ไม่ตรงกัน!');
            }

            // ตรวจสอบ token
            $stmt = $conn->prepare("SELECT email FROM users WHERE reset_token = ? AND token_expires_at > NOW()");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                throw new Exception('Token ไม่ถูกต้องหรือหมดอายุ!');
            }

            $row = $result->fetch_assoc();
            $email = $row['email'];

            // อัปเดตรหัสผ่านและลบ token
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expires_at = NULL WHERE email = ?");
            $stmt->bind_param("ss", $hashedNewPassword, $email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(['status' => 'success', 'message' => 'อัปเดตรหัสผ่านสำเร็จ']);
            } else {
                throw new Exception('เกิดข้อผิดพลาดในการอัปเดต!');
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit();
    }
    exit();
}

// ถ้าไม่ใช่ POST request และมี token ใน URL แสดงฟอร์มเปลี่ยนรหัสผ่าน
if (isset($_GET['token'])) {
    ?>
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ตั้งรหัสผ่านใหม่</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <link href="../Assets/CSS/change_password.css" rel="stylesheet">
        <!-- เพิ่ม SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- เพิ่ม specialchar.js -->
        <script src="../Assets/JS/specialchar.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center min-vh-100 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="card glass-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <i class="fas fa-key ocean-icon"></i>
                                <h3 class="card-title mt-3">ตั้งรหัสผ่านใหม่</h3>
                            </div>
                            
                            <form id="changePasswordForm">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text ocean-input-icon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" class="form-control ocean-input" name="password" id="password" required placeholder="รหัสผ่านใหม่">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="input-group">
                                        <span class="input-group-text ocean-input-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                        <input type="password" class="form-control ocean-input" name="confirm_password" id="confirm_password" required placeholder="ยืนยันรหัสผ่านใหม่">
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-ocean btn-lg">
                                        <i class="fas fa-sync-alt me-2"></i>อัปเดตรหัสผ่าน
                                    </button>
                                </div>
                            </form>
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
                const token = this.querySelector('input[name="token"]').value;
                const password = this.querySelector('input[name="password"]').value;
                const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
                const errorMsg = document.getElementById('errorMessage');

                // ตรวจสอบความยาวรหัสผ่าน (8 ตัวอักษรขึ้นไป)
                if (password.length < 8) {
                    errorMsg.textContent = 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร';
                    errorMsg.classList.remove('d-none');
                    return;
                }

                if (password !== confirmPassword) {
                    errorMsg.textContent = 'รหัสผ่านไม่ตรงกัน';
                    errorMsg.classList.remove('d-none');
                    return;
                }

                try {
                    const response = await fetch('/learning01/Management01/Backend/forgot_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ token, password, confirmPassword })
                    });
                    
                    const data = await response.json();
                    if (data.status === 'success') {
                        // ใช้ SweetAlert2 แทน alert()
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ!',
                            text: 'เปลี่ยนรหัสผ่านสำเร็จ',
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../Frontend/login.php';
                            }
                        });
                    } else {
                        errorMsg.textContent = data.message;
                        errorMsg.classList.remove('d-none');
                    }
                } catch (error) {
                    errorMsg.textContent = 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' + error.message;
                    errorMsg.classList.remove('d-none');
                }
            });
        </script>
    </body>
    </html>
    <?php
    exit();
}

// ถ้าไม่ใช่ POST request และไม่มี token แสดงฟอร์มแรก
include '../Frontend/forgot_password_view.php';
?>