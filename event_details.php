<?php
session_start();
require "db.php";

/* =========================
   🔐 تحديد صفحة الرجوع حسب الدور
========================= */
$backPage = "login.php";

if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] === "admin") {
        $backPage = "notifications_admin.php";
    } else {
        $backPage = "notifications.php";
    }
}

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
$stmt = $pdo->prepare("SELECT 
    ce.*,
    s.name AS student_name,
    ct.type_name
FROM cheating_events ce
JOIN students s ON ce.student_id = s.student_id
JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
WHERE ce.event_id = :id
LIMIT 1");

$stmt->execute(["id" => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "الحدث غير موجود";
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل حالة الغش</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #2f6fed, #1e5dd8);
            min-height: 100vh;
            padding: 40px 20px;
            direction: rtl;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: #fff;
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        p {
            margin: 15px 0;
            padding: 12px;
            background: #f5f7fb;
            border-radius: 10px;
        }

        p strong {
            color: #2f6fed;
            margin-left: 10px;
        }

        .status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: bold;
        }

        .suspected {
            background: #fff3cd;
            color: #856404;
        }

        .confirmed {
            background: #f8d7da;
            color: #721c24;
        }

        .rejected {
            background: #d4edda;
            color: #155724;
        }

        img, video {
            width: 100%;
            margin-top: 20px;
            border-radius: 12px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #2f6fed;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #1e5dd8;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>📄 تفاصيل حالة الغش</h2>

        <p>
            <strong>الطالب:</strong>
            <?= htmlspecialchars($event["student_name"]) ?>
        </p>

        <p>
            <strong>نوع الغش:</strong>
            <?= htmlspecialchars($event["type_name"]) ?>
        </p>

        <p>
            <strong>الحالة:</strong>
            <span class="status <?= $event["status"] ?>">
                <?=
                $event["status"] === "suspected" ? "مشتبه" :
                ($event["status"] === "confirmed" ? "تم التأكيد" : "تم الإلغاء")
                ?>
            </span>
        </p>

        <p>
            <strong>نسبة الثقة:</strong>
            <?= $event["confidence_score"] ?>%
        </p>

        <p>
            <strong>وقت الحدث:</strong>
            <?= $event["event_time"] ?>
        </p>

        <!-- 📷 صورة -->
        <?php if (!empty($event["snapshot_path"])): ?>
            <img src="<?= htmlspecialchars($event["snapshot_path"]) ?>">
        <?php endif; ?>

        <!-- 🎥 فيديو -->
        <?php if (!empty($event["video_path"])): ?>
            <video controls>
                <source src="<?= htmlspecialchars($event["video_path"]) ?>" type="video/mp4">
            </video>
        <?php endif; ?>

        <!-- 🔙 زر الرجوع -->
        <a href="<?= $backPage ?>" class="btn-back">
            <i class="fa fa-arrow-left"></i> العودة للإشعارات
        </a>

    </div>
</div>

</body>
</html>