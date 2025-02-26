(function() {
    // ฟังก์ชันสำหรับปิด autocomplete
    function disableAutocomplete() {
      // เลือกทุก input elements
      const inputs = document.querySelectorAll('input');
      
      // วนลูปเพื่อปิด autocomplete ทุก input
      inputs.forEach(input => {
        input.setAttribute('autocomplete', 'off');
      });
      
      // สังเกตการเปลี่ยนแปลงใน DOM เพื่อจัดการกับ elements ที่เพิ่มเข้ามาใหม่
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.addedNodes.length) {
            mutation.addedNodes.forEach((node) => {
              if (node.nodeName === 'INPUT') {
                node.setAttribute('autocomplete', 'off');
              }
              // เช็ค input elements ที่อยู่ข้างใน node ที่เพิ่มเข้ามาใหม่
              if (node.querySelectorAll) {
                const newInputs = node.querySelectorAll('input');
                newInputs.forEach(input => {
                  input.setAttribute('autocomplete', 'off');
                });
              }
            });
          }
        });
      });
  
      // กำหนดค่า observer
      observer.observe(document.body, {
        childList: true,
        subtree: true
      });
    }
  
    // เรียกใช้ฟังก์ชันเมื่อ DOM โหลดเสร็จ
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', disableAutocomplete);
    } else {
      disableAutocomplete();
    }
  })();