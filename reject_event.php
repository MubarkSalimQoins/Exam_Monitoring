<?php
require "auth_supervisor.php";   // 🔐 نفس الحماية المستخدمة في التأكيد
require "db.php";                // الاتصال بقاعدة البيانات

/* السماح بطلب POST فقط */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: notifications.php"); // منع الدخول المباشر
    exit;
}

/* جلب رقم الحدث */
$event_id = intval($_POST["event_id"] ?? 0);

/* التحقق من صحة الرقم */
if ($event_id <= 0) {
    header("Location: notifications.php");
    exit;
}

/* تحديث الحالة إلى ملغي (فقط إذا كانت مشتبه) */
$stmt = $pdo->prepare("
    UPDATE cheating_events
    SET status = 'rejected'
    WHERE event_id = :id
    AND status = 'suspected'
");

$stmt->execute([
    "id" => $event_id
]);

/* الرجوع لصفحة الإشعارات */
header("Location: notifications.php");
exit;
