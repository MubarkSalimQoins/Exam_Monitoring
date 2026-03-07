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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل حالة الغش</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            padding: 40px 20px;
            direction: rtl;
            position: relative;
            overflow-x: hidden;
        }

        /* خلفية متحركة مع شبكة */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(90deg, rgba(13, 110, 253, 0.03) 1px, transparent 1px),
                linear-gradient(rgba(13, 110, 253, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        /* دوائر متوهجة */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.15), transparent 70%);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-100px, 100px) scale(1.1); }
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* الكارد الرئيسي */
        .card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.2);
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* العنوان */
        h2 {
            color: white;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(13, 110, 253, 0.3);
            position: relative;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 100px;
            height: 2px;
            background: #0d6efd;
            box-shadow: 0 0 10px #0d6efd;
        }

        /* معلومات الحدث */
        p {
            margin: 20px 0;
            font-size: 16px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            padding: 15px;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            border-right: 4px solid #0d6efd;
            transition: all 0.3s ease;
        }

        p:hover {
            background: rgba(13, 110, 253, 0.1);
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }

        p strong {
            color: #0d6efd;
            font-weight: 700;
            min-width: 120px;
            display: inline-block;
            font-size: 15px;
        }

        /* حالات الأحداث - Status Badges */
        .status {
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            font-size: 14px;
            letter-spacing: 0.5px;
            border: 2px solid;
            margin-right: 10px;
        }

        .status.suspected {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border-color: #ffc107;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.3);
        }

        .status.confirmed {
            background: rgba(25, 135, 84, 0.2);
            color: #10b981;
            border-color: #10b981;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
        }

        .status.rejected {
            background: rgba(220, 53, 69, 0.2);
            color: #ef4444;
            border-color: #ef4444;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
        }

        /* الصورة والفيديو */
        img, video {
            width: 100%;
            margin-top: 25px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(13, 110, 253, 0.3);
            transition: all 0.4s ease;
        }

        img:hover, video:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 50px rgba(13, 110, 253, 0.4);
            border-color: #0d6efd;
        }

        /* زر العودة */
        a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
            padding: 14px 28px;
            background: rgba(13, 110, 253, 0.2);
            color: #0d6efd;
            text-decoration: none;
            font-weight: 700;
            border-radius: 12px;
            border: 2px solid #0d6efd;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        a::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(13, 110, 253, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        a:hover::before {
            width: 300px;
            height: 300px;
        }

        a:hover {
            background: #0d6efd;
            color: white;
            transform: translateX(5px);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.5);
        }

        a i {
            transition: transform 0.3s ease;
        }

        a:hover i {
            transform: translateX(5px);
        }

        /* رسالة الخطأ */
        .error-message {
            background: rgba(220, 53, 69, 0.2);
            color: #ef4444;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #ef4444;
            text-align: center;
            font-weight: 700;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .card {
                padding: 25px 20px;
                border-radius: 20px;
            }

            h2 {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }

            p {
                flex-direction: column;
                align-items: flex-start;
                padding: 12px;
                font-size: 14px;
            }

            p strong {
                min-width: auto;
                margin-bottom: 5px;
            }

            .status {
                margin-right: 0;
                margin-top: 5px;
            }

            a {
                width: 100%;
                justify-content: center;
                padding: 12px 20px;
            }
        }

        /* تأثير توهج */
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(13, 110, 253, 0.3); }
            50% { box-shadow: 0 0 40px rgba(13, 110, 253, 0.6); }
        }

        /* Media Container */
        .media-container {
            margin-top: 30px;
            padding: 20px;
            background: rgba(15, 23, 42, 0.5);
            border-radius: 16px;
            border: 1px solid rgba(13, 110, 253, 0.2);
        }

        .media-container img,
        .media-container video {
            margin-top: 0;
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
                    <?= $event["status"] === "suspected" ? "مشتبه" : ($event["status"] === "confirmed" ? "تم التأكيد" : "تم الإلغاء") ?>
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

            <!-- صورة -->
            <?php if (!empty($event["snapshot_path"])): ?>
            <div class="media-container">
                <img src="<?= htmlspecialchars($event["snapshot_path"]) ?>" alt="صورة الغش">
            </div>
            <?php endif; ?>

            <!-- فيديو -->
            <?php if (!empty($event["video_path"])): ?>
            <div class="media-container">
                <video controls>
                    <source src="<?= htmlspecialchars($event["video_path"]) ?>" type="video/mp4">
                </video>
            </div>
            <?php endif; ?>

            <a href="notifications.php">
                <i class="fa-solid fa-arrow-left"></i>
                العودة للإشعارات
            </a>
        </div>
    </div>
</body>
</html>
