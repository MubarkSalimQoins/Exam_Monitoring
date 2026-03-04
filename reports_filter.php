<?php
require "db.php";

$reports = [];
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

if ($from && $to) {
    $sql = "
        SELECT
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
        ORDER BY ce.event_time DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':from'=>$from, ':to'=>$to]);
    $reports = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إدارة التقارير</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Cairo',sans-serif;
    background: linear-gradient(135deg,#e3ecff,#f8f9ff);
    min-height:100vh;
}
.dashboard-card{
    background:#fff;
    border-radius:18px;
    box-shadow:0 15px 40px rgba(0,0,0,.1);
}
.header-box{
    background:linear-gradient(135deg,#0d6efd,#5a8dee);
    color:#fff;
    border-radius:16px;
    padding:25px;
    margin-bottom:25px;
}
.btn-action{
    transition:.3s;
}
.btn-action:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,.2);
}
.table thead{
    background:#f1f4fb;
}
.badge-status{
    padding:7px 14px;
    font-size:13px;
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

    <div class="dashboard-card p-4">

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
                <select class="form-select">
                    <option>PDF</option>
                    <option>Excel</option>
                    <option>CSV</option>
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
            <button class="btn btn-success btn-action">
                <i class="fa-solid fa-file-export"></i> إصدار التقارير
            </button>

            <button class="btn btn-outline-danger btn-action" onclick="clearTable()">
                <i class="fa-solid fa-trash"></i> حذف من الواجهة
            </button>
        </div>

        <!-- الجدول -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center" id="previewTable">
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

                <?php if ($reports): foreach($reports as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= $r['student_number'] ?></td>
                        <td><?= $r['major'] ?></td>
                        <td><?= $r['level'] ?></td>
                        <td><?= $r['type_name'] ?></td>
                        <td>
                            <span class="badge badge-status
                                <?= $r['status']=='confirmed'?'bg-danger':($r['status']=='suspected'?'bg-warning text-dark':'bg-success') ?>">
                                <?= $r['status'] ?>
                            </span>
                        </td>
                        <td><?= $r['event_time'] ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" class="text-muted">
                            لا توجد بيانات — اختر فترة زمنية
                        </td>
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
        document.querySelector("#previewTable tbody").innerHTML=
        `<tr><td colspan="7" class="text-muted">تم حذف البيانات من الواجهة</td></tr>`;
    }
}
</script>

</body>
</html>