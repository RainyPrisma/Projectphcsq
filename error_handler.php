<?php
// error_handler.php
$error_code = $_SERVER['REDIRECT_STATUS'];
$error_messages = [
    404 => 'ไม่พบหน้าที่คุณต้องการ',
    500 => 'เซิร์ฟเวอร์มีปัญหา',
    403 => 'ไม่มีสิทธิ์เข้าถึง',
    402 => 'ต้องการการชำระเงิน',
    400 => 'คำขอไม่ถูกต้อง'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกิดข้อผิดพลาด <?php echo $error_code; ?></title>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        body {
            font-family: 'Prompt', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8e8e8; /* Pastel pink background */
            transition: background-color 0.5s;
        }

        .error-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            animation: fadeIn 0.8s ease-out;
            background: linear-gradient(135deg, #fff5f5 0%, #fff 100%);
        }

        .error-code {
            font-size: 72px;
            color: #ffa1a1; /* Pastel red */
            margin: 0;
            font-weight: bold;
            animation: float 3s ease-in-out infinite;
        }

        .error-message {
            font-size: 24px;
            color: #8f9dad; /* Pastel blue-grey */
            margin: 1rem 0;
            animation: fadeIn 0.8s ease-out 0.2s backwards;
        }

        .error-description {
            color: #b4b4b4;
            margin-bottom: 2rem;
            animation: fadeIn 0.8s ease-out 0.4s backwards;
        }

        .back-button {
            background-color: #a1c7ff; /* Pastel blue */
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
            font-weight: bold;
            animation: fadeIn 0.8s ease-out 0.6s backwards;
            box-shadow: 0 4px 15px rgba(161, 199, 255, 0.3);
        }

        .back-button:hover {
            background-color: #8eb8ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(161, 199, 255, 0.4);
        }

        .back-button:active {
            transform: translateY(0);
        }

        /* เพิ่ม animation เมื่อเกิด error */
        .error-container:hover .error-code {
            animation: shake 0.8s ease-in-out;
        }

        /* เพิ่ม gradient border */
        .error-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #ffd1d1, #d1e8ff, #ffd1ee);
            border-radius: 22px;
            z-index: -1;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code"><?php echo $error_code; ?></h1>
        <h2 class="error-message">
            <?php echo $error_messages[$error_code] ?? 'เกิดข้อผิดพลาดที่ไม่ทราบสาเหตุ'; ?>
        </h2>
        <p class="error-description">
            ขออภัยในความไม่สะดวก กรุณาลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ
        </p>
        <a href="http://localhost/learning01/Management01/Frontend/login.php" class="back-button">กลับสู่หน้าหลัก</a>
    </div>
</body>
</html>