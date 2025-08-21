<?php
// الحصول على IP
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
$fingerprint = isset($_POST['fingerprint']) ? $_POST['fingerprint'] : 'unknown';

// توليد معرف الجهاز
$device_id = hash('sha256', $ip . '-' . $fingerprint . '-' . $_SERVER['HTTP_USER_AGENT']);

// ملف التخزين
$logFile = "device_log.txt";
$alreadyExists = false;

// فحص هل الجهاز موجود سابقًا
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, $device_id) !== false) {
            $alreadyExists = true;
            break;
        }
    }
}

// لو موجود، نرفض الطلب
if ($alreadyExists) {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "blocked",
        "message" => "تم رفض الطلب: نفس الجهاز سبق له الطلب."
    ]);
    exit;
}

// إذا جديد → نسجله
$logLine = date('Y-m-d H:i:s') . " | IP: $ip | Fingerprint: $fingerprint | DeviceID: $device_id | UA: " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
file_put_contents($logFile, $logLine, FILE_APPEND);

// الرد بالقبول
header('Content-Type: application/json');
echo json_encode([
    "status" => "ok",
    "device_id" => $device_id
]);
?>
