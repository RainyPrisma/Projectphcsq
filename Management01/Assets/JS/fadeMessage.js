document.addEventListener('DOMContentLoaded', function() {
    var msg = document.getElementById('message');
    if (msg) {
        setTimeout(function() {
            msg.style.transition = 'opacity 1s';
            msg.style.opacity = '0';
            setTimeout(function() {
                msg.remove();
            }, 1000);
        }, 2000);
    }
});