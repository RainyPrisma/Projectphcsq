// weather.js
function updateWeather() {
    // พิกัดของจังหวัดสงขลา
    const latitude = 7.1898; // ละติจูด
    const longitude = 100.5951; // ลองจิจูด
    fetch(`https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current_weather=true&hourly=wind_speed_10m`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data); // ตรวจสอบข้อมูลที่ได้จาก API
            const current = data.current_weather || {};
            const hourly = data.hourly || {};
            const windSpeed = hourly.wind_speed_10m && hourly.wind_speed_10m.length > 0 ? hourly.wind_speed_10m[0] : null; // ตรวจสอบก่อนใช้งาน
            let seaCondition = 'สภาพทะเล: โหลดข้อมูลล้มเหลว';

            if (windSpeed !== null) {
                if (windSpeed < 5) {
                    seaCondition = 'สภาพทะเล: สงบ';
                } else if (windSpeed >= 5 && windSpeed <= 10) {
                    seaCondition = 'สภาพทะเล: ปานกลาง';
                } else {
                    seaCondition = 'สภาพทะเล: ลมแรง';
                }
            }

            document.getElementById('weatherStatus').textContent = seaCondition;
            document.getElementById('weatherUpdateTime').textContent = `อัพเดทล่าสุด: ${new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' })} น.`;
        })
        .catch(error => {
            console.error('Error fetching weather data:', error);
            document.getElementById('weatherStatus').textContent = 'สภาพทะเล: โหลดข้อมูลล้มเหลว';
        });
}

// เรียกข้อมูลเมื่อหน้าโหลด
document.addEventListener('DOMContentLoaded', function () {
    updateWeather();
    const intervalId = setInterval(() => {
        fetch(`https://api.open-meteo.com/v1/forecast?latitude=${7.1898}&longitude=${100.5951}&current_weather=true&hourly=wind_speed_10m`)
            .then(response => {
                if (response.ok) {
                    updateWeather();
                }
            })
            .catch(() => {
                console.log('Skipping weather update due to previous failure');
            });
    }, 3600000); // อัปเดตทุก 1 ชั่วโมง (3600000 ms)
});