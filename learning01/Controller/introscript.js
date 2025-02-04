function enterWebsite() {
    // ซ่อนหน้า Splash Screen
    document.querySelector(".splash-screen").style.display = "none";

    // เปลี่ยนเส้นทางไปยังหน้า login.php
    window.location.href = "login.php";
}
