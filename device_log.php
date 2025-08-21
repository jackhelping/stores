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

// توليد معرف الجهاز (يمكن دمج IP + fingerprint ثم عمل هاش)
$device_id = hash('sha256', $ip . '-' . $fingerprint . '-' . $_SERVER['HTTP_USER_AGENT']);

// تخزين البيانات في ملف نصي (يمكنك تغييره إلى قاعدة بيانات)
$logLine = date('Y-m-d H:i:s') . " | IP: $ip | Fingerprint: $fingerprint | DeviceID: $device_id | UA: " . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL;
file_put_contents("device_log.txt", $logLine, FILE_APPEND);

// استجابة عادية
header('Content-Type: application/json');
echo json_encode([
    "status" => "ok",
    "device_id" => $device_id
]);
?>
