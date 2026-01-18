<?php
session_start();
require "db.php";

header("Content-Type: application/json");

if (!isset($_POST["student_id"])) {
    echo json_encode([
        "status" => "error",
        "message" => "معرف الطالب غير موجود"
    ]);
    exit;
}

$student_id = (int) $_POST["student_id"];

$stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
$success = $stmt->execute([$student_id]);

if ($success) {
    echo json_encode([
        "status" => "success",
        "message" => "تم حذف الطالب بنجاح"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "فشل حذف الطالب"
    ]);
}
