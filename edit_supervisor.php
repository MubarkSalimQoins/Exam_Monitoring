<?php
session_start();
require "db.php";

header('Content-Type: application/json');

$id       = intval($_POST['supervisor_id'] ?? 0);
$name     = trim($_POST['name']            ?? '');
$password = trim($_POST['password']        ?? '');
$role     = trim($_POST['role']            ?? '');

if (!$id || !$name || !in_array($role, ['supervisor', 'admin'])) {
    echo json_encode(['success' => false, 'message' => 'بيانات غير صحيحة']);
    exit;
}

if ($password !== '') {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE supervisors SET name=?, password=?, role=? WHERE supervisor_id=?");
    $stmt->execute([$name, $hashed, $role, $id]);
} else {
    $stmt = $pdo->prepare("UPDATE supervisors SET name=?, role=? WHERE supervisor_id=?");
    $stmt->execute([$name, $role, $id]);
}

echo json_encode(['success' => true, 'message' => 'تم تحديث البيانات بنجاح']);
