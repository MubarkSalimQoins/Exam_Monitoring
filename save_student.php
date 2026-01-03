<?php
session_start();
require "db.php";

/*
----------------------------------
1️⃣ حماية الصفحة (لاحقًا نربط ملف الحماية)
----------------------------------
*/
// مثال لاحق:
// require "supervisor_protect.php";


/*
----------------------------------
2️⃣ التأكد أن الطلب POST
----------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_student.php");
    exit;
}


/*
----------------------------------
3️⃣ استقبال البيانات
----------------------------------
*/
$name           = trim($_POST["name"] ?? "");
$student_number = trim($_POST["student_number"] ?? "");
$level          = trim($_POST["level"] ?? "");
$major          = trim($_POST["major"] ?? "");


/*
----------------------------------
4️⃣ التحقق من البيانات
----------------------------------
*/
if ($name === "" || $student_number === "" || $level === "" || $major === "") {
    die("❌ جميع الحقول مطلوبة");
}


/*
----------------------------------
5️⃣ التحقق من رقم القيد (عدم التكرار)
----------------------------------
*/
$check = $pdo->prepare("SELECT student_id FROM students WHERE student_number = ?");
$check->execute([$student_number]);

if ($check->rowCount() > 0) {
    die("❌ رقم القيد موجود مسبقًا");
}


/*
----------------------------------
6️⃣ معالجة رفع الصورة
----------------------------------
*/
$image_path = null;

if (!empty($_FILES["student_image"]["name"])) {

    $allowed_types = ["image/jpeg", "image/png", "image/jpg"];
    $file_type = $_FILES["student_image"]["type"];

    if (!in_array($file_type, $allowed_types)) {
        die("❌ نوع الصورة غير مدعوم");
    }

    // إنشاء اسم فريد للصورة
    $ext = pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION);
    $image_name = uniqid("student_") . "." . $ext;

    $upload_dir  = "uploads/students/";
    $image_path  = $upload_dir . $image_name;

    if (!move_uploaded_file($_FILES["student_image"]["tmp_name"], $image_path)) {
        die("❌ فشل رفع الصورة");
    }
}


/*
----------------------------------
7️⃣ إدخال البيانات في قاعدة البيانات
----------------------------------
*/
$stmt = $pdo->prepare("
    INSERT INTO students 
    (student_number, name, major, level, image_path, created_at)
    VALUES
    (:student_number, :name, :major, :level, :image_path, NOW())
");

$stmt->execute([
    ":student_number" => $student_number,
    ":name"           => $name,
    ":major"          => $major,
    ":level"          => $level,
    ":image_path"     => $image_path
]);


/*
----------------------------------
8️⃣ الرجوع لصفحة الطلاب
----------------------------------
*/
header("Location: add_student.php?success=1");
exit;
