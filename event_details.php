<?php
require "auth_supervisor.php"; // 🔐 استدعاء ملف الحماية
require "db.php";              // 📡 الاتصال بقاعدة البيانات

/* =========================
   📌 التحقق من رقم الحدث
========================= */
$event_id = intval($_GET["id"] ?? 0);

if ($event_id <= 0) {
    echo "رقم حدث غير صالح";
    exit;
}

/* =========================
   📥 جلب تفاصيل الحدث
========================= */
$stmt = $pdo->prepare("
    SELECT 
        ce.*,
        s.name AS student_name,
        ct.type_name
    FROM cheating_events ce
    JOIN students s ON ce.student_id = s.student_id
    JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
    WHERE ce.event_id = :id
    LIMIT 1
");

$stmt->execute(["id" => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

/* في حال عدم وجود الحدث */
if (!$event) {
    echo "الحدث غير موجود";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>تفاصيل حالة الغش</title>

<link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Cairo', sans-serif;
    background: #f4f6f9;
    padding: 20px;
    direction: rtl;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    max-width: 600px;
    margin: auto;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

h2 {
    margin-bottom: 15px;
}

p {
    margin: 6px 0;
    font-size: 14px;
}

.status {
    font-weight: bold;
}

.status.suspected { color: #856404; }
.status.confirmed { color: #155724; }
.status.rejected  { color: #721c24; }

img, video {
    width: 100%;
    margin-top: 15px;
    border-radius: 8px;
}

a {
    display: inline-block;
    margin-top: 15px;
    color: #2f6fed;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>
<body>

<div class="card">
    <h2>📄 تفاصيل حالة الغش</h2>

    <p><strong>الطالب:</strong> <?= htmlspecialchars($event["student_name"]) ?></p>
    <p><strong>نوع الغش:</strong> <?= htmlspecialchars($event["type_name"]) ?></p>

    <p>
        <strong>الحالة:</strong>
        <span class="status <?= $event["status"] ?>">
            <?= $event["status"] === "suspected" ? "مشتبه" : ($event["status"] === "confirmed" ? "تم التأكيد" : "تم الإلغاء") ?>
        </span>
    </p>

    <p><strong>نسبة الثقة:</strong> <?= $event["confidence_score"] ?>%</p>
    <p><strong>وقت الحدث:</strong> <?= $event["event_time"] ?></p>

    <!-- صورة -->
    <?php if (!empty($event["snapshot_path"])): ?>
        <img src="<?= htmlspecialchars($event["snapshot_path"]) ?>" alt="صورة الغش">
    <?php endif; ?>

    <!-- فيديو -->
    <?php if (!empty($event["video_path"])): ?>
        <video controls>
            <source src="<?= htmlspecialchars($event["video_path"]) ?>" type="video/mp4">
        </video>
    <?php endif; ?>

    <a href="notifications.php">⬅ العودة للإشعارات</a>
</div>

</body>
</html>
