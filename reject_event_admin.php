<?php
session_start();
require "db.php";

/* 🔐 التحقق من صلاحية المدير */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

/* السماح فقط بـ POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: notifications_admin.php");
    exit;
}

/* جلب ID */
$event_id = intval($_POST["event_id"] ?? 0);

if ($event_id <= 0) {
    header("Location: notifications_admin.php");
    exit;
}

/* تحديث الحالة */
$stmt = $pdo->prepare("
    UPDATE cheating_events
    SET status = 'rejected'
    WHERE event_id = :id
");

$stmt->execute([
    "id" => $event_id
]);

/* 🔥 إعادة التوجيه (مهم جداً) */
header("Location: notifications_admin.php");
exit;

