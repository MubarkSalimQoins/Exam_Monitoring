<?php
// get_student.php
require "db.php";
header("Content-Type: application/json; charset=utf-8");

// ❌ احذف أي die() أو echo في البداية

// ✅ استقبال الـ ID من GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => '❌ معرّف الطالب مطلوب'
    ]);
    exit;
}

$student_id = (int)$_GET['id'];

try {
    // جلب بيانات الطالب
    $stmt = $pdo->prepare("
        SELECT student_id, name, student_number, level, major, image_path 
        FROM students 
        WHERE student_id = ?
    ");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode([
            'status'  => 'error',
            'message' => '❌ الطالب غير موجود'
        ]);
        exit;
    }

    // ✅ إرجاع البيانات
    echo json_encode([
        'status' => 'success',
        'data'   => $student
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => '❌ خطأ في قاعدة البيانات'
    ]);
}