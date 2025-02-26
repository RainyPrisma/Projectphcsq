<?php   
include '../Database/config.php';
include '../Frontend/modal.php';
// รับค่าจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบว่ามีค่าที่ส่งมาครบหรือไม่
    if (isset($_POST['username']) && isset($_POST['email']) && 
        isset($_POST['password']) && isset($_POST['confirm_password']) && 
        isset($_POST['phone_number'])) {

        $user = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // ตรวจสอบความถูกต้องของข้อมูล
        $errors = [];

        // ตรวจสอบชื่อผู้ใช้
        if (empty($user)) {
            $errors[] = 'กรุณากรอกชื่อผู้ใช้';
        } elseif (strlen($user) < 3) {
            $errors[] = 'ชื่อผู้ใช้ต้องมีความยาวอย่างน้อย 3 ตัวอักษร';
        }

        // ตรวจสอบอีเมล
        if (empty($email)) {
            $errors[] = 'กรุณากรอกอีเมล';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
        } elseif (preg_match('/[!#$%^&*(),?":{}|<>\/]/', $email)) {
            $errors[] = 'อีเมลห้ามมีอักษรพิเศษที่ไม่ถูกต้อง';
        }

        // ตรวจสอบรหัสผ่าน
        if (empty($password)) {
            $errors[] = 'กรุณากรอกรหัสผ่าน';
        } elseif (strlen($password) < 8) {
            $errors[] = 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร';
        } elseif ($password !== $confirm_password) {
            $errors[] = 'รหัสผ่านไม่ตรงกัน';
        }

        // ตรวจสอบเบอร์โทรศัพท์
        if (empty($phone_number)) {
            $errors[] = 'กรุณากรอกเบอร์โทรศัพท์';
        } elseif (!preg_match('/^[0-9]{10}$/', $phone_number)) {
            $errors[] = 'เบอร์โทรศัพท์ต้องเป็นตัวเลข 10 หลัก';
        }

        // ตรวจสอบว่ามีอีเมลหรือชื่อผู้ใช้ซ้ำหรือไม่
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $check_stmt->bind_param("ss", $email, $user);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = 'อีเมลหรือชื่อผู้ใช้นี้มีอยู่แล้ว';
        }
        $check_stmt->close();

        // หากมี errors ให้แสดงข้อผิดพลาดผ่าน Modal
        if (!empty($errors)) {
            $error_message = implode('<br>', $errors);
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    showModal('แจ้งเตือน', '<?php echo $error_message; ?>', function() {
                        window.history.back();
                    });
                });
            </script>
            <?php
            exit();
        }

        // หากผ่านการตรวจสอบทั้งหมด
        $pass = password_hash($password, PASSWORD_DEFAULT);

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone_number, password) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            showModal('Error', 'Database preparation failed: ' . $conn->error);
            exit();
        }

        // ผูกค่าและรันคำสั่ง SQL
        $stmt->bind_param("ssss", $user, $email, $phone_number, $pass);

        if ($stmt->execute()) {
            ?>
            <script>
                function showAndRedirect() {
                    // แสดง Modal
                    document.getElementById('customModal').style.display = 'block';
                    document.getElementById('modalTitle').textContent = 'สำเร็จ';
                    document.getElementById('modalMessage').textContent = 'ลงทะเบียนสำเร็จ! กรุณาเข้าสู่ระบบ';
                    
                    // เพิ่ม event listener สำหรับปุ่มตกลง
                    document.querySelector('.modal-button').onclick = function() {
                        hideModal(); // เรียกฟังก์ชัน hideModal ที่มีอยู่แล้ว
                        window.location.href = '../Frontend/login.php';
                    };
                }
                
                // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเสร็จ
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', showAndRedirect);
                } else {
                    showAndRedirect();
                }
            </script>
            <?php
            exit();
        } else {
            showModal('Error', 'Registration failed: ' . $stmt->error);
        }

        $stmt->close();
    } else {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showModal('แจ้งเตือน', 'กรุณากรอกข้อมูลให้ครบทุกช่อง', function() {
                    window.history.back();
                });
            });
        </script>
        <?php
        exit();
    }
}

$conn->close();

// ฟังก์ชั่นสำหรับแสดง Modal
function showModal($title, $message, $callback = null) {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            document.getElementById('modalTitle').textContent = '<?php echo $title; ?>';
            document.getElementById('modalBody').innerHTML = '<?php echo $message; ?>';
            
            <?php if ($callback): ?>
            document.getElementById('modalCloseBtn').onclick = function() {
                modal.hide();
                <?php echo $callback; ?>();
            };
            <?php endif; ?>
            
            modal.show();
        });
    </script>
    <?php
}
?>