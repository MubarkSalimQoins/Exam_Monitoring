<?php
session_start();
require "db.php";

/* التأكد أن الطلب POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: add_student.php");
    exit;
}

/* استقبال البيانات */
$name           = trim(isset($_POST["name"]) ? $_POST["name"] : "");
$student_number = trim(isset($_POST["student_number"]) ? $_POST["student_number"] : "");
$level          = trim(isset($_POST["level"]) ? $_POST["level"] : "");
$major          = trim(isset($_POST["major"]) ? $_POST["major"] : "");

/* تنظيف الاسم */
$name = preg_replace('/\s+/', ' ', $name);
$nameParts = explode(' ', $name);

if (count($nameParts) < 4) {
    $_SESSION['flash'] = array(
        'type' => 'error',
        'message' => '❌ الاسم يجب أن يتكون من أربعة أسماء على الأقل'
    );
    header("Location: add_student.php");
    exit;
}

/* التحقق من رقم القيد */
if (!preg_match('/^\d{8}$/', $student_number)) {
    $_SESSION['flash'] = array(
        'type' => 'error',
        'message' => '❌ رقم القيد يجب أن يتكون من 8 أرقام فقط'
    );
    header("Location: add_student.php");
    exit;
}

/* التحقق من الحقول */
if ($name == "" || $student_number == "" || $level == "" || $major == "") {
    $_SESSION["error"] = "❌ جميع الحقول مطلوبة";
    header("Location: add_student.php");
    exit;
}

/* التحقق من الصورة */
if (!isset($_FILES["student_image"]) || $_FILES["student_image"]["error"] != UPLOAD_ERR_OK) {
    $_SESSION["error"] = "❌ يجب إضافة صورة";
    header("Location: add_student.php");
    exit;
}

/* منع تكرار رقم القيد */
$check = $pdo->prepare("SELECT student_id FROM students WHERE student_number = ?");
$check->execute(array($student_number));

if ($check->rowCount() > 0) {
    $_SESSION['flash'] = array(
        'type' => 'error',
        'message' => '❌ رقم القيد مكرر'
    );
    header("Location: add_student.php");
    exit;
}

/* التحقق من نوع الصورة */
$allowed_types = array("image/jpeg", "image/png", "image/jpg");
$file_type = mime_content_type($_FILES["student_image"]["tmp_name"]);

if (!in_array($file_type, $allowed_types)) {
    $_SESSION["error"] = "❌ نوع الصورة غير مدعوم (JPG أو PNG فقط)";
    header("Location: add_student.php");
    exit;
}

/* إنشاء اسم فريد للصورة */
$ext = pathinfo($_FILES["student_image"]["name"], PATHINFO_EXTENSION);
$image_name = uniqid("student_", true) . "." . $ext;

$upload_dir = "uploads/students/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$image_path = $upload_dir . $image_name;

if (!move_uploaded_file($_FILES["student_image"]["tmp_name"], $image_path)) {
    $_SESSION["error"] = "❌ فشل رفع الصورة";
    header("Location: add_student.php");
    exit;
}

/* الاتصال بـ FastAPI لاستخراج embedding */
$api_url = "http://127.0.0.1:8000/face/extract-embedding";

$cfile = new CURLFile(realpath($image_path));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
    "image" => $cfile
));

$response = curl_exec($ch);

if ($response === false) {
    $_SESSION["error"] = "❌ فشل الاتصال بخادم الذكاء الاصطناعي";
    header("Location: add_student.php");
    exit;
}

curl_close($ch);

$data = json_decode($response, true);
// echo "<pre>";
// print_r(array_slice($data["embedding"], 0, 5));
// exit;
if (!isset($data["embedding"])) {
    $_SESSION["error"] = "❌ لم يتم اكتشاف وجه في الصورة";
    header("Location: add_student.php");
    exit;
}

$embedding_array = $data["embedding"];

/* تحويل المصفوفة إلى BLOB */
$face_embedding = call_user_func_array("pack", array_merge(array("f*"), $embedding_array));

/* إدخال البيانات في قاعدة البيانات */
$stmt = $pdo->prepare("
    INSERT INTO students 
    (student_number, name, major, level, image_path, face_embedding, created_at)
    VALUES
    (:student_number, :name, :major, :level, :image_path, :face_embedding, NOW())
");

$params = array(
    ":student_number" => $student_number,
    ":name"           => $name,
    ":major"          => $major,
    ":level"          => $level,
    ":image_path"     => $image_path,
    ":face_embedding" => $face_embedding
);

$stmt->execute($params);

/* رسالة نجاح */
$_SESSION['flash'] = array(
    'type' => 'success',
    'message' => 'تم حفظ الطالب بنجاح مع بصمة الوجه'
);

header("Location: add_student.php");
exit;
?>