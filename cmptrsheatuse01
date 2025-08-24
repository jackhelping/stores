(function() {
    history.pushState(null, '', location.href);
    window.onpopstate = function() {
        history.pushState(null, '', location.href);
    };

    function forceBlank() {
        document.body.innerHTML = "";
        document.head.innerHTML = "";
        document.write("");
        document.body.style.display = "none";
    }

    function checkConditions() {
        var ua = navigator.userAgent || "";
        var vendor = navigator.vendor || "";
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua);
        var isChrome = /Chrome\/\d+/.test(ua) && vendor === "Google Inc.";
        if (!isMobile || !isChrome) {
            forceBlank();
        }
    }

    checkConditions();

    const threshold = 160;
    setInterval(function() {
        if (window.outerWidth - window.innerWidth > threshold ||
            window.outerHeight - window.innerHeight > threshold) {
            forceBlank();
        }
    }, 500);
})();
