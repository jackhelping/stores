// التحقق من نوع الجهاز والمتصفح
function checkConditions() {
    var ua = navigator.userAgent || "";
    var vendor = navigator.vendor || "";

    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
    var isChrome = /Chrome\/\d+/.test(ua) && vendor === "Google Inc.";

    if (!isMobile || !isChrome) {
        forceBlank();
    }
}

// تنفيذ الفحص عند تشغيل الصفحة
checkConditions();
