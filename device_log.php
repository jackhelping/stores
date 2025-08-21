<?php
// تحديد نوع الإخراج JavaScript لأننا سنطبع كود JS مباشرة
header('Content-Type: application/javascript');

// ----------------- جزء PHP -----------------
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip = getUserIP();
$logFile = __DIR__ . "/device_log.txt";
?>

// ----------------- جزء JavaScript -----------------
(function(){
  // تحميل مكتبة FingerprintJS
  FingerprintJS.load().then(fp => {
    fp.get().then(result => {
      const fingerprint = result.visitorId;
      const ip = "<?php echo $ip; ?>";
      const device_id = sha256(ip + "-" + fingerprint);

      // تحقق من التكرار عبر طلب متزامن صغير إلى PHP
      fetch("<?php echo basename(__FILE__); ?>?check=" + device_id)
        .then(res => res.json())
        .then(data => {
          if (data.status === "duplicate") {
            showDuplicateMessage();
          } else {
            registerDevice(device_id, fingerprint, ip);
            showSuccessMessage();
          }
        });
    });
  });

  // دالة تسجيل الجهاز في ملف PHP (GET بسيط)
  function registerDevice(device_id, fingerprint, ip){
    fetch("<?php echo basename(__FILE__); ?>?save=" + device_id +
          "&finger=" + fingerprint + "&ip=" + ip);
  }

  // دالة لإظهار رسالة النجاح
  function showSuccessMessage(){
    const overlay = document.getElementById("overlay");
    const notification = document.getElementById("notification");
    if(overlay && notification){
      overlay.style.display = "block";
      notification.style.display = "block";
      let timer = 15;
      const timerEl = document.getElementById("timer");
      const interval = setInterval(()=>{
        timer--;
        if(timerEl) timerEl.textContent = timer;
        if(timer <= 0){
          clearInterval(interval);
          overlay.style.display = "none";
          notification.style.display = "none";
        }
      },1000);
    }
  }

  // دالة لإظهار رسالة التكرار غير القابلة للإغلاق
  function showDuplicateMessage(){
    const overlay = document.createElement("div");
    overlay.style.position="fixed";
    overlay.style.top="0";
    overlay.style.left="0";
    overlay.style.width="100%";
    overlay.style.height="100%";
    overlay.style.background="rgba(0,0,0,0.7)";
    overlay.style.zIndex="99999";
    overlay.style.display="flex";
    overlay.style.justifyContent="center";
    overlay.style.alignItems="center";
    overlay.innerHTML = '<div style="background:#c0392b;padding:30px;border-radius:10px;color:#fff;font-size:20px;text-align:center;max-width:90%;">تم تسجيل طلبك مسبقًا من هذا الجهاز.<br>لا يمكنك إرسال طلب جديد.</div>';
    document.body.appendChild(overlay);
  }

  // دالة بسيطة لتوليد SHA-256 (JavaScript)
  function sha256(str){
    const buf = new TextEncoder("utf-8").encode(str);
    return crypto.subtle.digest("SHA-256", buf).then(hash=>{
      return Array.from(new Uint8Array(hash))
        .map(b => b.toString(16).padStart(2,"0"))
        .join("");
    });
  }
})();
<?php
// ----------------- جزء PHP لتسجيل البيانات -----------------

// تحقق التكرار
if(isset($_GET['check'])){
    $device_id = $_GET['check'];
    $isDuplicate = false;
    if(file_exists($logFile)){
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($lines as $line){
            if(strpos($line, $device_id) !== false){
                $isDuplicate = true;
                break;
            }
        }
    }
    echo json_encode(["status"=>$isDuplicate?"duplicate":"new"]);
    exit;
}

// حفظ البيانات
if(isset($_GET['save'])){
    $device_id = $_GET['save'];
    $finger = isset($_GET['finger'])?$_GET['finger']:"unknown";
    $ip = isset($_GET['ip'])?$_GET['ip']:"unknown";
    $logLine = date('Y-m-d H:i:s')." | IP: $ip | Fingerprint: $finger | DeviceID: $device_id".PHP_EOL;
    file_put_contents($logFile, $logLine, FILE_APPEND);
    exit;
}
?>
