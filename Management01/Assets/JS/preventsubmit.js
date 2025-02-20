// ฟังก์ชั่นสำหรับป้องกันการ submit ซ้ำ
class FormSubmitProtection {
    constructor() {
        this.submitting = false;
        this.submitTimeout = 2000; // ระยะเวลาล็อค (milliseconds)
        this.submitTimers = new Map();
    }

    // เช็คและป้องกันการ submit ซ้ำ
    protect(formId) {
        if (this.submitting) {
            console.warn('กำลังดำเนินการส่งข้อมูล กรุณารอสักครู่...');
            return false;
        }

        // ถ้ามี timer ที่กำลังนับอยู่ ให้ยกเลิกการ submit
        if (this.submitTimers.has(formId)) {
            console.warn('กรุณารอสักครู่ก่อนที่จะส่งข้อมูลอีกครั้ง');
            return false;
        }

        this.submitting = true;

        // ตั้ง timer สำหรับปลดล็อค
        const timer = setTimeout(() => {
            this.submitting = false;
            this.submitTimers.delete(formId);
        }, this.submitTimeout);

        this.submitTimers.set(formId, timer);
        return true;
    }

    // รีเซ็ตสถานะ (ใช้เมื่อต้องการยกเลิกการป้องกัน)
    reset(formId) {
        if (this.submitTimers.has(formId)) {
            clearTimeout(this.submitTimers.get(formId));
            this.submitTimers.delete(formId);
        }
        this.submitting = false;
    }
}

// ตัวอย่างการใช้งาน
const formProtection = new FormSubmitProtection();

// ตัวอย่างการใช้กับ form
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // ตรวจสอบการ submit ซ้ำ
            if (!formProtection.protect('loginForm')) {
                return;
            }

            // โค้ดสำหรับส่งข้อมูล login
            submitLoginData()
                .then(response => {
                    // ทำงานเมื่อส่งข้อมูลสำเร็จ
                    console.log('Login successful');
                })
                .catch(error => {
                    // กรณีเกิดข้อผิดพลาด ให้รีเซ็ตการป้องกัน
                    formProtection.reset('loginForm');
                    console.error('Login failed:', error);
                });
        });
    }
});

// ฟังก์ชั่นจำลองการส่งข้อมูล
function submitLoginData() {
    return new Promise((resolve, reject) => {
        // จำลองการส่งข้อมูลไปยังเซิร์ฟเวอร์
        setTimeout(() => {
            resolve({ success: true });
        }, 1000);
    });
}