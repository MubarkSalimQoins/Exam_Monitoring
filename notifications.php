<?php
session_start();                 // تشغيل الجلسة
require "db.php";                // الاتصال بقاعدة البيانات

/* =========================
   🔐 حماية الصفحة
========================= */

/* التحقق من تسجيل الدخول */
if ($_SESSION["role"] !== "supervisor") {
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>غير مصرح</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            direction: rtl;
        }
        .box {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 400px;
        }
        .box h2 {
            color: #c62828;
            margin-bottom: 10px;
        }
        .box p {
            color: #555;
            font-size: 14px;
        }
    </style>
    <meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>
    <div class="box">
        <h2>❌ ليس لديك صلاحية</h2>
        <p>ليس لديك صلاحية الدخول إلى هذه الصفحة</p>
        <p>سيتم إعادتك إلى صفحة تسجيل الدخول...</p>
    </div>
</body>
</html>
<?php
    exit;
}

/* =========================
   📥 جلب بيانات الأحداث
========================= */

$sql = "SELECT 
    ce.event_id,
    ce.status,
    ce.confidence_score,
    ce.event_time,
    s.name AS student_name,
    ct.type_name
FROM cheating_events ce
JOIN students s ON ce.student_id = s.student_id
JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
ORDER BY ce.event_time DESC";

$stmt = $pdo->prepare($sql);     // تحضير الاستعلام
$stmt->execute();                // تنفيذ الاستعلام
$events = $stmt->fetchAll(PDO::FETCH_ASSOC); // جلب النتائج

/* =========================
   🎨 دالة عرض حالة الحدث
========================= */

function renderStatus($status) {
    if ($status === "confirmed") {
        return '<span class="status confirmed">تم التأكيد</span>';
    } elseif ($status === "rejected") {
        return '<span class="status rejected">تم الإلغاء</span>';
    } else {
        return '<span class="status suspected">مشتبه</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإشعارات</title>
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
            padding: 30px 20px;
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
            max-width: 1500px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* العنوان الرئيسي - تصميم جديد */
        h2 {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(10, 88, 202, 0.1));
            backdrop-filter: blur(10px);
            border: 2px solid rgba(13, 110, 253, 0.3);
            color: white;
            padding: 35px 40px;
            border-radius: 25px;
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 35px;
            box-shadow: 
                0 20px 60px rgba(13, 110, 253, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            50%, 100% { left: 100%; }
        }

        /* Card للجدول - تصميم جديد */
        .table-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 
                0 25px 70px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(13, 110, 253, 0.2);
            overflow: hidden;
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

        /* الجدول */
        table {
            width: 100%;
            background: transparent;
            border-collapse: separate;
            border-spacing: 0;
        }

        th, td {
            padding: 20px 15px;
            text-align: center;
            font-size: 15px;
        }

        th {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.3), rgba(10, 88, 202, 0.3));
            backdrop-filter: blur(10px);
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
            border-bottom: 2px solid rgba(13, 110, 253, 0.5);
            position: relative;
        }

        th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #0d6efd, transparent);
        }

        td {
            color: #e2e8f0;
            background: rgba(30, 41, 59, 0.4);
            border-bottom: 1px solid rgba(13, 110, 253, 0.1);
        }

        tbody tr {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        tbody tr::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.2), transparent);
            transition: width 0.4s ease;
            z-index: -1;
        }

        tbody tr:hover::before {
            width: 100%;
        }

        tbody tr:hover {
            transform: translateX(-5px);
            box-shadow: 5px 0 20px rgba(13, 110, 253, 0.3);
        }

        tbody tr:hover td {
            background: rgba(13, 110, 253, 0.1);
            color: white;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* حالات الأحداث - Badges بتصميم جديد */
        .status {
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            display: inline-block;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            border: 2px solid;
        }

        .status::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .status:hover::before {
            width: 200px;
            height: 200px;
        }

        .suspected {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border-color: #ffc107;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
        }

        .confirmed {
            background: rgba(25, 135, 84, 0.2);
            color: #10b981;
            border-color: #10b981;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
        }

        .rejected {
            background: rgba(220, 53, 69, 0.2);
            color: #ef4444;
            border-color: #ef4444;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        }

        /* الأزرار - تصميم جديد */
        .btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: 2px solid;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            margin: 3px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-confirm {
            background: rgba(25, 135, 84, 0.2);
            color: #10b981;
            border-color: #10b981;
        }

        .btn-confirm:hover {
            background: #10b981;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.5);
        }

        .btn-reject {
            background: rgba(220, 53, 69, 0.2);
            color: #ef4444;
            border-color: #ef4444;
        }

        .btn-reject:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.5);
        }

        .btn-details {
            background: rgba(13, 110, 253, 0.2);
            color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn-details:hover {
            background: #0d6efd;
            color: white;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.5);
        }

        /* زر معطل */
        .btn-disabled {
            background: rgba(100, 116, 139, 0.2);
            color: #64748b;
            border-color: #64748b;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
            background: rgba(100, 116, 139, 0.2);
            color: #64748b;
        }

        .btn-disabled::before {
            display: none;
        }

        form {
            display: inline;
        }

        /* رسالة "لا توجد إشعارات" */
        .no-data {
            padding: 80px 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 18px;
            font-weight: 600;
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            body {
                padding: 15px 10px;
            }

            h2 {
                font-size: 1.6rem;
                padding: 25px 20px;
            }

            .table-card {
                border-radius: 20px;
            }

            th, td {
                padding: 15px 10px;
                font-size: 13px;
            }

            .btn {
                padding: 8px 14px;
                font-size: 11px;
                margin: 2px;
            }

            .status {
                padding: 7px 14px;
                font-size: 11px;
            }

            /* جعل الجدول قابل للتمرير أفقياً */
            .table-card {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }

        /* تحسين Scrollbar */
        .table-card::-webkit-scrollbar {
            height: 10px;
        }

        .table-card::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 10px;
        }

        .table-card::-webkit-scrollbar-thumb {
            background: #0d6efd;
            border-radius: 10px;
            border: 2px solid rgba(30, 41, 59, 0.5);
        }

        .table-card::-webkit-scrollbar-thumb:hover {
            background: #0d6efd;
            opacity: 0.8;
        }

        /* تأثير توهج للعناصر */
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(13, 110, 253, 0.3); }
            50% { box-shadow: 0 0 40px rgba(13, 110, 253, 0.6); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📢 إشعارات الغش</h2>
        
        <div class="table-card">
            <table>
                <tr>
                    <th>الطالب</th>
                    <th>نوع الغش</th>
                    <th>نسبة الثقة</th>
                    <th>الحالة</th>
                    <th>الوقت</th>
                    <th>الإجراءات</th>
                </tr>
                
                <?php if (!$events): ?>
                <tr>
                    <td colspan="6" class="no-data">لا توجد إشعارات</td>
                </tr>
                <?php endif; ?>
                
                <?php foreach ($events as $event): ?>
                <tr>
                    <!-- هذا يطبع لك حق الجدول يجيبهم مثل رقم الطالب ونوع الغش وغيرها -->
                    <td><?= htmlspecialchars($event["student_name"]) ?></td>
                    <td><?= htmlspecialchars($event["type_name"]) ?></td>
                    <td><?= $event["confidence_score"] ?>%</td>
                    <td><?= renderStatus($event["status"]) ?></td>
                    <td><?= $event["event_time"] ?></td>
                    <td>
                        <?php if ($event["status"] === "suspected"): ?>
                        <!-- ✅ أزرار مفعلة (مشتبه) -->
                        <form action="confirm_event.php" method="POST">
                            <input type="hidden" name="event_id" value="<?= $event["event_id"] ?>">
                            <button class="btn btn-confirm">تأكيد</button>
                        </form>
                        <form action="reject_event.php" method="POST">
                            <input type="hidden" name="event_id" value="<?= $event["event_id"] ?>">
                            <button class="btn btn-reject">إلغاء</button>
                        </form>
                        <?php else: ?>
                        <!-- 🚫 أزرار معطلة (تم التأكيد / الإلغاء) -->
                        <button class="btn btn-disabled" disabled>تأكيد</button>
                        <button class="btn btn-disabled" disabled>إلغاء</button>
                        <?php endif; ?>
                        
                        <!-- زر التفاصيل (دائمًا متاح) -->
                        <a href="event_details.php?id=<?= $event["event_id"] ?>">
                            <button class="btn btn-details">التفاصيل</button>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
