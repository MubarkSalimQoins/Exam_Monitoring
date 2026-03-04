<?php
require "db.php";

$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

$reports = [];

if ($from && $to) {
    $sql = "
    SELECT
        students.name AS student_name,
        students.student_number,
        students.major,
        students.level,
        cheating_types.type_name,
        cheating_events.status,
        cheating_events.event_time
    FROM cheating_events
    JOIN students 
        ON cheating_events.student_id = students.student_id
    JOIN cheating_types 
        ON cheating_events.cheating_type_id = cheating_types.cheating_type_id
    WHERE DATE(cheating_events.event_time) BETWEEN :from AND :to
    ORDER BY cheating_events.event_time DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':from' => $from,
        ':to'   => $to
    ]);

    $reports = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>نتائج التقارير</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f4f6f9;
        }
    </style>
</head>
<body>

<div class="container my-4">

    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold">نتائج التقارير</h5>
        <a href="reports_filter.php" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>اسم الطالب</th>
                        <th>رقم القيد</th>
                        <th>التخصص</th>
                        <th>المستوى</th>
                        <th>نوع الغش</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($reports): ?>
                    <?php foreach ($reports as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                            <td><?= $row['student_number'] ?></td>
                            <td><?= $row['major'] ?></td>
                            <td><?= $row['level'] ?></td>
                            <td><?= $row['type_name'] ?></td>
                            <td>
                                <span class="badge
                                    <?= $row['status']=='confirmed' ? 'bg-danger' :
                                        ($row['status']=='suspected' ? 'bg-warning text-dark' : 'bg-success') ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td><?= $row['event_time'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-muted">
                            لا توجد نتائج
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>