<?php
require "db.php";

$stmt = $pdo->query("SELECT student_id, face_embedding FROM students LIMIT 2");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

function unpack_embedding($blob) {
    return unpack("f*", $blob);
}

$emb1 = unpack_embedding($students[0]['face_embedding']);
$emb2 = unpack_embedding($students[1]['face_embedding']);

echo "<pre>";

echo "Student 1 - First 5 values:\n";
print_r(array_slice($emb1, 0, 5));

echo "\nStudent 2 - First 5 values:\n";
print_r(array_slice($emb2, 0, 5));

echo "</pre>";
?>