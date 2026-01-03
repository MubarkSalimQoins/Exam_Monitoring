<?php
require "auth_supervisor.php";   // 🔐 استدعاء الحماية
require "db.php";                // قاعدة البيانات

/* السماح بطلب POST فقط */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: notifications.php");
    exit;
}

/* جلب رقم الحدث */
$event_id = intval($_POST["event_id"] ?? 0);

/* التحقق من الرقم */
if ($event_id <= 0) {
    header("Location: notifications.php");
    exit;
}

/* تحديث الحالة (فقط إذا كانت مشتبه) */
$stmt = $pdo->prepare("
    UPDATE cheating_events
    SET status = 'confirmed'
    WHERE event_id = :id
    AND status = 'suspected'
");

$stmt->execute([
    "id" => $event_id
]);

/* الرجوع للإشعارات */
header("Location: notifications.php");
exit;
