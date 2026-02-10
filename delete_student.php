<?php
session_start();
require "db.php";

header("Content-Type: application/json");

if (!isset($_POST['student_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "❌ معرف الطالب غير موجود"
    ]);
    exit;
}

$student_id = (int) $_POST['student_id'];

/*
----------------------------------
1️⃣ جلب مسار الصورة
----------------------------------
*/
$stmt = $pdo->prepare("SELECT image_path FROM students WHERE student_id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo json_encode([
        "status" => "error",
        "message" => "❌ الطالب غير موجود"
    ]);
    exit;
}

$imagePath = $student['image_path'];

/*
----------------------------------
2️⃣ حذف الصورة من السيرفر
----------------------------------
*/
if (!empty($imagePath) && file_exists($imagePath)) {
    unlink($imagePath);
}

/*
----------------------------------
3️⃣ حذف الطالب من قاعدة البيانات
----------------------------------
*/
$delete = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
$delete->execute([$student_id]);

echo json_encode([
    "status" => "success",
    "message" => "✅ تم حذف الطالب وصورته بنجاح"
]);
exit;













































// header("Content-Type: application/json");

// if (!isset($_POST["student_id"])) {
//     echo json_encode([
//         "status" => "error",
//         "message" => "معرف الطالب غير موجود"
//     ]);
//     exit;
// }

// $student_id = (int) $_POST["student_id"];

// $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = ?");
// $success = $stmt->execute([$student_id]);

// if ($success) {
//     echo json_encode([
//         "status" => "success",
//         "message" => "تم حذف الطالب بنجاح"
//     ]);
// } else {
//     echo json_encode([
//         "status" => "error",
//         "message" => "فشل حذف الطالب"
//     ]);
// }
