$isDuplicate = false;

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, $device_id) !== false) {
            $isDuplicate = true;   // ← هنا الشرط
            break;
        }
    }
}

if ($isDuplicate) {
    // نفس الجهاز طلب من قبل → إشعار مكرر
    echo json_encode([
        "status" => "duplicate",
        "device_id" => $device_id
    ]);
} else {
    // تسجيل الطلب لأول مرة
    $logLine = date('Y-m-d H:i:s') . " | IP: $ip | Fingerprint: $fingerprint | DeviceID: $device_id" . PHP_EOL;
    file_put_contents($logFile, $logLine, FILE_APPEND);
    
    echo json_encode([
        "status" => "ok",
        "device_id" => $device_id
    ]);
}
