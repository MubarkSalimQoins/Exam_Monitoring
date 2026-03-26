<?php
session_start();
require "db.php";

header('Content-Type: application/json');

$id = intval($_POST['supervisor_id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'معرّف غير صحيح']);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM supervisors WHERE supervisor_id = ?");
$stmt->execute([$id]);

echo json_encode(['success' => true, 'message' => 'تم حذف المراقب بنجاح']);
