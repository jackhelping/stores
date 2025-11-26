// اكتشاف فتح أدوات المطور (DevTools)
const threshold = 160;

setInterval(function () {
    if (
        window.outerWidth - window.innerWidth > threshold ||
        window.outerHeight - window.innerHeight > threshold
    ) {
        forceBlank();
    }
}, 500);
