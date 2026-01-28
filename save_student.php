<?php
session_start();
require "db.php";

/*
----------------------------------
1️⃣ التأكد أن الطلب POST
----------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_student.php");
    exit;
}

/*
----------------------------------
2️⃣ استقبال البيانات من الفورم
----------------------------------
*/
$name           = trim($_POST["name"] ?? "");
$student_number = trim($_POST["student_number"] ?? "");
$level          = trim($_POST["level"] ?? "");
$major          = trim($_POST["major"] ?? "");

$name = trim($name);                     // حذف الفراغات من البداية والنهاية
$name = preg_replace('/\s+/', ' ', $name); // مسافة واحدة فقط بين الأسماء
//منشان الاسم يتكون من اربعه اسماء فقط مع مسافه بين كل اسم
// التحقق أن الاسم يحتوي على 4 كلمات على الأقل (يدعم العربية)
$nameParts = explode(' ', $name);

if (count($nameParts) < 4) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => '❌ الاسم يجب أن يتكون من أربعة أسماء على الأقل مع مسافة واحدة بين كل اسم'
    ];

    $_SESSION['old'] = [
        'student_number' => $student_number,
        'level'          => $level,
        'major'          => $major
    ];

    $_SESSION['error_field'] = 'name';
    header("Location: add_student.php");
    exit;
}


// التحقق أن رقم القيد 8 أرقام فقط
if (!preg_match('/^\d{8}$/', $student_number)) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => '❌ رقم القيد يجب أن يتكون من 8 أرقام فقط ولايوجد احرف'
    ];

    // حفظ القيم الصحيحة فقط
    $_SESSION['old'] = [
        'name'  => $name,
        'level' => $level,
        'major' => $major
        // لا نحفظ رقم القيد ❌
    ];
    $_SESSION['error_field'] = 'student_number';
    header("Location: add_student.php");
    exit;
}



/*
----------------------------------
3️⃣ التحقق من الحقول النصية
----------------------------------
*/
if ($name === "" || $student_number === "" || $level === "" || $major === "") {
    $_SESSION["error"] = "❌ جميع الحقول مطلوبة";
    header("Location: add_student.php");
    exit;
}

/*
----------------------------------
4️⃣ منع الحفظ بدون صورة
----------------------------------
*/
if (
    !isset($_FILES["student_image"]) ||
    $_FILES["student_image"]["error"] !== UPLOAD_ERR_OK
) {
    $_SESSION["error"] = "❌ يجب عليك إضافة صورة للطالب";
    header("Location: add_student.php");
    exit;
}

/*
----------------------------------
5️⃣ التحقق من عدم تكرار رقم القيد
----------------------------------
*/
$check = $pdo->prepare("SELECT student_id FROM students WHERE student_number = ?");
$check->execute([$student_number]);
//تكرار رقم القيد موقف استبدلناه بلي تحت
// if ($check->rowCount() > 0) {
//     $_SESSION["error"] = "❌ رقم القيد مكرر، الرجاء إدخال رقم آخر";
//     header("Location: add_student.php");
//     exit;
// }
if ($check->rowCount() > 0) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => '❌ رقم القيد مكرر، الرجاء إدخال رقم آخر'
    ];

    // نحتفظ بكل القيم ما عدا رقم القيد
    $_SESSION['old'] = [
        'name'  => $name,
        'level' => $level,
        'major' => $major
    ];

    // تحديد الحقل الذي فيه الخطأ
    $_SESSION['error_field'] = 'student_number';

    header("Location: add_student.php");
    exit;
}

/*
----------------------------------
6️⃣ معالجة الصورة (شرح مهم 👇)
----------------------------------

▪ نتحقق من نوع الصورة الحقيقي (ليس الامتداد فقط)
▪ نسمح فقط بـ JPG / PNG
▪ ننشئ اسم فريد للصورة
▪ نحفظ المسار في قاعدة البيانات فقط

*/
$allowed_types = ["image/jpeg", "image/png", "image/jpg"];

// قراءة نوع الملف الحقيقي من محتواه
$file_type = mime_content_type($_FILES["student_image"]["tmp_name"]);

if (!in_array($file_type, $allowed_types)) {
    $_SESSION["error"] = "❌ نوع الصورة غير مدعوم (JPG أو PNG فقط)";
    header("Location: add_student.php");
    exit;
}

// إنشاء اسم فريد للصورة
$ext = pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION);
$image_name = uniqid("student_", true) . "." . $ext;

// مسار الرفع
$upload_dir = "uploads/students/";

// إنشاء المجلد إن لم يكن موجود
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$image_path = $upload_dir . $image_name;

// نقل الصورة من الملف المؤقت إلى مجلد المشروع
if (!move_uploaded_file($_FILES["student_image"]["tmp_name"], $image_path)) {
    $_SESSION["error"] = "❌ فشل رفع الصورة";
    header("Location: add_student.php");
    exit;
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
8️⃣ رسالة نجاح + الرجوع لنفس الصفحة
----------------------------------
*/
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'تم حفظ الطالب بنجاح'
];
header("Location: add_student.php");
exit;
