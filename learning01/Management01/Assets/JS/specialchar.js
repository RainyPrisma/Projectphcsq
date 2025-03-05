document.addEventListener('DOMContentLoaded', function() {
    // เลือกทุก input และ textarea ที่ต้องการบล็อคอักษรพิเศษ
    const inputElements = document.querySelectorAll('input, textarea');

    inputElements.forEach(element => {
        // ฟังก์ชันตรวจสอบอักษรพิเศษ
        function validateInput(e) {
            // รูปแบบอักษรที่อนุญาต (ตัวอักษรไทย อังกฤษ ตัวเลข และช่องว่าง)
            const allowedPattern = /^[ก-๙a-zA-Z0-9\s]*$/;

            // ป้องกันการป้อนอักษรพิเศษในทันที
            if (!allowedPattern.test(e.key)) {
                e.preventDefault();
                return false;
            }
        }

        // ฟังก์ชันกรองข้อความที่มีอยู่แล้ว
        function filterExistingText() {
            element.value = element.value.replace(/[^ก-๙a-zA-Z0-9\s]/g, '');
        }

        // เพิ่ม Event Listener เพื่อป้องกันอักษรพิเศษ
        element.addEventListener('keypress', validateInput);
        element.addEventListener('input', filterExistingText);

        // ป้องกันการวาง (Paste) ข้อความที่มีอักษรพิเศษ
        element.addEventListener('paste', function(e) {
            e.preventDefault();
            
            // รับข้อความที่ถูกวาง
            let pastedText = e.clipboardData.getData('text');
            
            // กรองอักษรพิเศษออก
            let cleanedText = pastedText.replace(/[^ก-๙a-zA-Z0-9\s]/g, '');
            
            // แทรกข้อความที่ผ่านการกรอง
            document.execCommand('insertText', false, cleanedText);
        });
    });
});