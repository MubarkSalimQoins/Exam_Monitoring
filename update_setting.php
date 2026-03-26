<?php
session_start();
require "db.php";

header('Content-Type: application/json');

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo json_encode(['success' => false, 'message' => 'ليس لديك صلاحية']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setting_id'], $_POST['setting_value'])) {
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = :value WHERE id = :id");
    $updated = $stmt->execute([
        'value' => $_POST['setting_value'],
        'id' => $_POST['setting_id']
    ]);

    if ($updated) {
        echo json_encode(['success' => true, 'message' => 'تم حفظ التعديل بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء حفظ التعديل']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'طلب غير صالح']);