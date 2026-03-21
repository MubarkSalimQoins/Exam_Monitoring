<?php
session_start();
require "db.php";

/* 🔐 التحقق من صلاحية المدير */
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
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
        <p>هذه الصفحة مخصصة للمدير فقط</p>
        <p>سيتم إعادتك إلى تسجيل الدخول...</p>
    </div>
</body>
</html>
<?php
    exit;
}

/* 📊 جلب البيانات */
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

$stmt = $pdo->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* 🟢 عرض الحالة */
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
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إشعارات المدير</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="notifications.css">
</head>
<body>

    <div class="hamburger-btn" onclick="toggleSidebar()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h3 class="sidebar-title">جامعة شبوة</h3>
            <p class="sidebar-subtitle">نظام مراقبة الاختبارات</p>
        </div>

        <div class="sidebar-menu">
            <a href="notifications_admin.php">
                <i class="fa-solid fa-bell"></i>
                <span>الإشعارات</span>
            </a>
            <a href="reports_filter.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>التقارير</span>
            </a>
            <a href="add_student.php">
                <i class="fa-solid fa-user-plus"></i>
                <span>إضافة طالب</span>
            </a>
            <a href="login.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </a>
        </div>
    </div>

    <div class="container">
        <h2>👑 إشعارات الغش (المدير)</h2>

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
                    <td><?= htmlspecialchars($event["student_name"]) ?></td>
                    <td><?= htmlspecialchars($event["type_name"]) ?></td>
                    <td><?= $event["confidence_score"] ?>%</td>
                    <td><?= renderStatus($event["status"]) ?></td>
                    <td><?= $event["event_time"] ?></td>

                    <td>
                        <!-- 🔥 المدير يقدر يعدل دائماً -->
                        <form action="confirm_event_admin.php" method="POST">
                            <input type="hidden" name="event_id" value="<?= $event["event_id"] ?>">
                            <button class="btn btn-confirm">تأكيد</button>
                        </form>

                        <form action="reject_event_admin.php" method="POST">
                            <input type="hidden" name="event_id" value="<?= $event["event_id"] ?>">
                            <button class="btn btn-reject">إلغاء</button>
                        </form>

                        <a href="event_details.php?id=<?= $event["event_id"] ?>">
                            <button class="btn btn-details">التفاصيل</button>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const hamburger = document.querySelector('.hamburger-btn');

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            hamburger.classList.toggle('active');
        }
    </script>

</body>
</html>
```
