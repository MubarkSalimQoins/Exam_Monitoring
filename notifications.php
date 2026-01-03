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

$sql = "
SELECT 
    ce.event_id,
    ce.status,
    ce.confidence_score,
    ce.event_time,
    s.name AS student_name,
    ct.type_name
FROM cheating_events ce
JOIN students s ON ce.student_id = s.student_id
JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
ORDER BY ce.event_time DESC
";

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
    <title>الإشعارات</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }

        table {
            width: 100%;
            background: #fff;
            border-radius: 10px;
            border-collapse: collapse;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            background: #2f6fed;
            color: #fff;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .suspected { background: #fff3cd; color: #856404; }
        .confirmed { background: #d4edda; color: #155724; }
        .rejected  { background: #f8d7da; color: #721c24; }

        .btn {
            padding: 6px 10px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            cursor: pointer;
            margin: 2px;
            color: #fff;
        }

        .btn-confirm { background: #28a745; }
        .btn-reject  { background: #dc3545; }
        .btn-details { background: #2f6fed; }

        /* زر معطل */
        .btn-disabled {
            background: #bdbdbd;
            cursor: not-allowed;
            opacity: 0.7;
        }

        form { display: inline; }
    </style>
</head>
<body>

<h2>📢 إشعارات الغش</h2>

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
            <td colspan="6">لا توجد إشعارات</td>
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

</body>
</html>
