<?php
require "db.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح']);
    exit;
}

$id = (int)($_POST['student_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$level = trim($_POST['level'] ?? '');
$major = trim($_POST['major'] ?? '');

if ($id <= 0 || empty($name) || empty($level) || empty($major)) {
    echo json_encode(['status' => 'error', 'message' => 'جميع الحقول مطلوبة']);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT image_path FROM students WHERE student_id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'الطالب غير موجود']);
        exit;
    }

    $currentImage = $student['image_path'];
    $newEmbeddingBlob = null;
    $imageChanged = false;

    /*
    ======================================
    في حال رفع صورة جديدة
    ======================================
    */
    if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] === UPLOAD_ERR_OK) {

        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileType = mime_content_type($_FILES['student_image']['tmp_name']);

        if (!in_array($fileType, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'نوع الصورة غير مدعوم']);
            exit;
        }

        $ext = pathinfo($_FILES['student_image']['name'], PATHINFO_EXTENSION);
        $newName = 'student_' . $id . '_' . time() . '.' . $ext;
        $uploadDir = 'uploads/students/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newPath = $uploadDir . $newName;

        if (!move_uploaded_file($_FILES['student_image']['tmp_name'], $newPath)) {
            echo json_encode(['status' => 'error', 'message' => 'فشل رفع الصورة']);
            exit;
        }

        /*
        ======================================
        استخراج embedding من FastAPI
        ======================================
        */
        $api_url = "http://127.0.0.1:8000/face/extract-embedding";
        $cfile = new CURLFile(realpath($newPath));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ["image" => $cfile]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data["embedding"])) {
            unlink($newPath);
            echo json_encode(['status' => 'error', 'message' => 'لم يتم اكتشاف وجه في الصورة']);
            exit;
        }

        $embedding_array = $data["embedding"];
        $newEmbeddingBlob = call_user_func_array("pack", array_merge(array("f*"), $embedding_array));

        // حذف الصورة القديمة
        if (!empty($currentImage) && file_exists($currentImage)) {
            unlink($currentImage);
        }

        $currentImage = $newPath;
        $imageChanged = true;
    }

    /*
    ======================================
    التحديث
    ======================================
    */

    if ($imageChanged) {
        $update = $pdo->prepare("
            UPDATE students 
            SET name = ?, level = ?, major = ?, image_path = ?, face_embedding = ? 
            WHERE student_id = ?
        ");

        $success = $update->execute([$name, $level, $major, $currentImage, $newEmbeddingBlob, $id]);

    } else {

        $update = $pdo->prepare("
            UPDATE students 
            SET name = ?, level = ?, major = ? 
            WHERE student_id = ?
        ");

        $success = $update->execute([$name, $level, $major, $id]);
    }

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'تم التعديل بنجاح'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في التحديث']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ: ' . $e->getMessage()]);
}