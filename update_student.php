<?php
// update_student.php
require "db.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح']);
    exit;
}

// البيانات الأساسية
$id = (int)($_POST['student_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$level = trim($_POST['level'] ?? '');
$major = trim($_POST['major'] ?? '');

// التحقق من البيانات
if ($id <= 0 || empty($name) || empty($level) || empty($major)) {
    echo json_encode(['status' => 'error', 'message' => 'جميع الحقول مطلوبة']);
    exit;
}

try {
    // جلب بيانات الطالب
    $stmt = $pdo->prepare("SELECT image_path FROM students WHERE student_id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        echo json_encode(['status' => 'error', 'message' => 'الطالب غير موجود']);
        exit;
    }
    
    $currentImage = $student['image_path'];
    $hasCurrentImage = (!empty($currentImage) && $currentImage !== 'assets/img/default.png');
    
    // حالة: حذف الصورة
    if (isset($_POST['delete_image']) && $_POST['delete_image'] === '1') {
        // التحقق: إذا كان هناك صورة أصلية وحذفها دون إضافة جديدة
        if ($hasCurrentImage && 
            (!isset($_FILES['student_image']) || $_FILES['student_image']['error'] !== UPLOAD_ERR_OK)) {
            
            echo json_encode(['status' => 'error', 'message' => 'لا يمكن حذف الصورة دون إضافة صورة جديدة']);
            exit;
        }
        
        // حذف الصورة القديمة
        if ($currentImage && file_exists($currentImage) && $currentImage !== 'assets/img/default.png') {
            unlink($currentImage);
        }
        $currentImage = 'assets/img/default.png';
    }
    
    // حالة: رفع صورة جديدة - ⚠️ هذا هو الكود المفقود
    if (isset($_FILES['student_image']) && $_FILES['student_image']['error'] === UPLOAD_ERR_OK) {
        // التحقق من نوع الملف
        $allowed = ['image/jpeg', 'image/jpg', 'image/png'];
        $fileType = mime_content_type($_FILES['student_image']['tmp_name']);
        
        if (!in_array($fileType, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'نوع الصورة غير مدعوم (يجب أن تكون jpg, jpeg, png)']);
            exit;
        }
        
        // التحقق من حجم الملف (5MB كحد أقصى)
        if ($_FILES['student_image']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'حجم الصورة كبير جداً. الحد الأقصى 5MB']);
            exit;
        }
        
        // إنشاء اسم فريد للصورة
        $ext = pathinfo($_FILES['student_image']['name'], PATHINFO_EXTENSION);
        $newName = 'student_' . $id . '_' . time() . '.' . $ext;
        $uploadDir = 'uploads/students/';
        
        // إنشاء المجلد إذا لم يكن موجوداً
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $newPath = $uploadDir . $newName;
        
        // نقل الصورة
        if (move_uploaded_file($_FILES['student_image']['tmp_name'], $newPath)) {
            // حذف الصورة القديمة
            if ($hasCurrentImage && file_exists($currentImage) && $currentImage !== 'assets/img/default.png') {
                unlink($currentImage);
            }
            $currentImage = $newPath;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل في رفع الصورة']);
            exit;
        }
    }
    
    // التحقق النهائي: يجب أن تكون هناك صورة
    if (empty($currentImage) || $currentImage === 'assets/img/default.png') {
        echo json_encode(['status' => 'error', 'message' => 'يجب أن يكون للطالب صورة']);
        exit;
    }
    
    // التحديث
    $update = $pdo->prepare("UPDATE students SET name = ?, level = ?, major = ?, image_path = ? WHERE student_id = ?");
    $success = $update->execute([$name, $level, $major, $currentImage, $id]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'تم التعديل بنجاح',
            'new_image_path' => $currentImage // أضف هذا للإرجاع
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'فشل في التحديث']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
}