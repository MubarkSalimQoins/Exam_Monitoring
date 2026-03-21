<?php
require "auth_admin.php";   // 🔐 حماية المدير
require "db.php";

/* السماح بطلب POST فقط */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: notifications_admin.php");
    exit;
}

/* جلب رقم الحدث */
$event_id = intval($_POST["event_id"] ?? 0);

if ($event_id <= 0) {
    header("Location: notifications_admin.php");
    exit;
}

/* 🔥 المدير يقدر يعدل بأي وقت (بدون شرط الحالة) */
$stmt = $pdo->prepare("
    UPDATE cheating_events
    SET status = 'confirmed'
    WHERE event_id = :id
");

$stmt->execute([
    "id" => $event_id
]);

header("Location: notifications_admin.php");
exit;