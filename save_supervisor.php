<?php
ob_start();
session_start();
require "db.php";
ob_clean();

header('Content-Type: application/json; charset=utf-8');

$name     = trim($_POST['name']     ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? '');

if (!$name || !$password || !in_array($role, ['supervisor', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'يرجى تعبئة جميع الحقول بشكل صحيح']);
    exit;
}

try {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO supervisors (name, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$name, $hashed, $role]);
    echo json_encode(['success' => true, 'message' => 'تمت إضافة المراقب بنجاح']);
} catch (Exception $e) {
    $code = $e->getCode();
    if ($code == 23000) {
        echo json_encode(['success' => false, 'message' => 'هذا الاسم مسجل مسبقاً أو يوجد تكرار في البيانات']);
    } else {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات: ' . $e->getMessage()]);
    }
}
