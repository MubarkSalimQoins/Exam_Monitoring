<?php
require "db.php";

$reports = [];
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

if ($from && $to) {
    $sql = "SELECT
        s.name,
        s.student_number,
        s.major,
        s.level,
        ct.type_name,
        ce.status,
        ce.event_time
    FROM cheating_events ce
    JOIN students s ON ce.student_id = s.student_id
    JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
    WHERE DATE(ce.event_time) BETWEEN :from AND :to
    ORDER BY ce.event_time DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':from'=>$from, ':to'=>$to]);
    $reports = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة التقارير</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #e8f0fe 0%, #f0f4ff 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        /* خلفية متحركة */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(13, 110, 253, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(13, 110, 253, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
        }

        /* صندوق العنوان */
        .header-box {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            border-radius: 20px;
            padding: 35px 30px;
            margin-bottom: 30px;
            box-shadow: 0 15px 40px rgba(13, 110, 253, 0.3);
            position: relative;
            overflow: hidden;
        }

        .header-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-box::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .header-box h4 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .header-box p {
            font-size: 1.1rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* الكارد الرئيسي */
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
            padding: 35px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.12);
        }

        /* تحسين Labels */
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* تحسين حقول الإدخال */
        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus,
        .form-select:focus {
            background: white;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            transform: translateY(-2px);
        }

        /* تحسين الأزرار */
        .btn {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(13, 110, 253, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid #0d6efd;
            color: #0d6efd;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: #0d6efd;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #198754 0%, #157347 100%);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(25, 135, 84, 0.4);
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        }

        .btn i {
            margin-left: 8px;
        }

        /* خط فاصل احترافي */
        hr {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, #0d6efd, transparent);
            opacity: 0.2;
            margin: 30px 0;
        }

        /* تحسين الجدول */
        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
            background: white;
        }

        .table thead {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
        }

        .table thead th {
            padding: 18px 15px;
            font-weight: 700;
            font-size: 15px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 16px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #2c3e50;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(90deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.02));
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* تحسين الـ Badges */
        .badge-status {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }

        .bg-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
        }

        .bg-success {
            background: linear-gradient(135deg, #198754 0%, #157347 100%) !important;
            box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
            color: white !important;
        }

        /* رسالة "لا توجد بيانات" */
        .text-muted {
            color: #6c757d !important;
            font-size: 15px;
            padding: 40px 20px !important;
        }

        /* تحسينات للشاشات الصغيرة */
        @media (max-width: 768px) {
            .header-box {
                padding: 25px 20px;
            }

            .header-box h4 {
                font-size: 1.5rem;
            }

            .header-box p {
                font-size: 0.95rem;
            }

            .dashboard-card {
                padding: 20px;
            }

            .btn {
                padding: 10px 18px;
                font-size: 14px;
            }

            .table thead th,
            .table tbody td {
                padding: 12px 10px;
                font-size: 13px;
            }

            .d-flex.gap-2 {
                flex-direction: column;
            }

            .d-flex.gap-2 .btn {
                width: 100% !important;
            }
        }

        /* أنيميشن للصفحة */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-card {
            animation: fadeInUp 0.6s ease;
        }

        .header-box {
            animation: fadeInUp 0.5s ease;
        }

        /* تحسين مظهر الـ Select */
        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%230d6efd' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 0.75rem center;
            background-size: 16px 12px;
        }

        /* تحسين الـ Date Input */
        input[type="date"] {
            position: relative;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: invert(47%) sepia(89%) saturate(2476%) hue-rotate(201deg) brightness(98%) contrast(98%);
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <!-- العنوان -->
        <div class="header-box text-center">
            <h4 class="fw-bold mb-1">📊 نظام التقارير</h4>
            <p class="mb-0">فلترة وعرض وتصدير تقارير الغش</p>
        </div>

        <div class="dashboard-card">
            <!-- الفلترة -->
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">من تاريخ</label>
                    <input type="date" name="from" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">صيغة التقرير</label>
                    <select class="form-select" name="format">
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>
                <!-- الأزرار جنب بعض -->
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary w-100 btn-action">
                        <i class="fa-solid fa-filter"></i> عرض
                    </button>
                    <?php if ($from && $to): ?>
                    <a href="reports_results.php?from=<?= $from ?>&to=<?= $to ?>"
                       class="btn btn-outline-primary w-100 btn-action">
                        <i class="fa-solid fa-table"></i> التقارير
                    </a>
                    <?php endif; ?>
                </div>
            </form>

            <hr class="my-4">

            <!-- أزرار التحكم -->
            <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                <a href="export_reports.php?from=<?= $from ?>&to=<?= $to ?>&format=<?= $_GET['format'] ?? 'pdf' ?>"
                   class="btn btn-success btn-action">
                    <i class="fa-solid fa-file-export"></i> إصدار التقارير
                </a>
                <button class="btn btn-outline-danger btn-action" onclick="clearTable()">
                    <i class="fa-solid fa-trash"></i> حذف من الواجهة
                </button>
            </div>

            <!-- الجدول -->
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center" id="previewTable">
                    <thead>
                        <tr>
                            <th>الطالب</th>
                            <th>رقم القيد</th>
                            <th>التخصص</th>
                            <th>المستوى</th>
                            <th>نوع الغش</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reports): 
                            foreach($reports as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td><?= $r['student_number'] ?></td>
                            <td><?= $r['major'] ?></td>
                            <td><?= $r['level'] ?></td>
                            <td><?= $r['type_name'] ?></td>
                            <td>
                                <?php
// تحويل الحالة للعرض بالعربي
                                   $status_ar = $r['status'] === 'confirmed' ? 'مؤكدة' :
                                   ($r['status'] === 'rejected' ? 'ملغية' : 'مشتبه');
                                   $badge_class = $r['status'] === 'confirmed' ? 'bg-success' :
                                   ($r['status'] === 'rejected' ? 'bg-danger' : 'bg-warning text-dark');
                                     ?>
                                    <span class="badge badge-status <?= $badge_class ?>">
                                     <?= $status_ar ?>
                                         </span>
                            </td>
                            <td><?= $r['event_time'] ?></td>
                        </tr>
                        <?php endforeach; 
                        else: ?>
                        <tr>
                            <td colspan="7" class="text-muted">لا توجد بيانات — اختر فترة زمنية</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function clearTable(){
            if(confirm("هل تريد حذف النتائج من الواجهة فقط؟")){
                document.querySelector("#previewTable tbody").innerHTML=`<tr><td colspan="7" class="text-muted">تم حذف البيانات من الواجهة</td></tr>`;
            }
        }
    </script>
</body>
</html>
