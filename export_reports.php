<?php
// report_mpdf.php - نسخة محسنة باستخدام mPDF
require "db.php";
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

// جلب البيانات من GET
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;
$format = $_GET['format'] ?? 'pdf';

$reports = [];
$total = 0;
$confirmed = 0;
$suspected = 0;
$dismissed = 0;

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
    $stmt->execute([':from' => $from, ':to' => $to]);
    $reports = $stmt->fetchAll();
    
    $total = count($reports);
    $confirmed = count(array_filter($reports, fn($r) => $r['status'] === 'confirmed'));
    $suspected = count(array_filter($reports, fn($r) => $r['status'] === 'suspected'));
    $dismissed = count(array_filter($reports, fn($r) => $r['status'] === 'dismissed'));
}

// تصدير CSV إذا طلب
if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reports.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['الطالب', 'رقم القيد', 'التخصص', 'المستوى', 'نوع الغش', 'الحالة', 'التاريخ']);
    foreach ($reports as $r) {
        fputcsv($output, [
            $r['name'],
            $r['student_number'],
            $r['major'],
            $r['level'],
            $r['type_name'],
            $r['status'],
            $r['event_time']
        ]);
    }
    fclose($output);
    exit;
}

// إنشاء PDF باستخدام mPDF
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 25,
    'margin_bottom' => 20,
    'default_font' => 'dejavusans',
    'directionality' => 'rtl' // تفعيل الكتابة من اليمين لليسار
]);

// تعيين معلومات المستند
$mpdf->SetCreator('نظام إدارة التقارير');
$mpdf->SetAuthor('نظام إدارة الغش');
$mpdf->SetTitle('تقرير حالات الغش');

// محتوى HTML مع CSS متقدم
$html = '
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: "dejavusans", sans-serif;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .report-container {
            max-width: 100%;
            padding: 0;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 20px;
            border-radius: 15px 15px 0 0;
            margin-bottom: 25px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.95;
        }
        .date-badge {
            background: rgba(255,255,255,0.2);
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
        }
        .stats-grid {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
            direction: ltr;
        }
        .stat-card {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 18px 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .stat-card.total .stat-number { color: #3498db; }
        .stat-card.confirmed .stat-number { color: #e74c3c; }
        .stat-card.suspected .stat-number { color: #f39c12; }
        .stat-card.dismissed .stat-number { color: #2ecc71; }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            direction: rtl;
        }
        th {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            font-weight: bold;
            padding: 12px 8px;
            text-align: center;
            border: none;
            font-size: 13px;
        }
        td {
            padding: 10px 8px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            background-color: white;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover td {
            background-color: #f8f9fa;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 12px;
            min-width: 70px;
        }
        .status-confirmed {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }
        .status-suspected {
            background: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
            color: white;
        }
        .status-dismissed {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
        }
        .footer {
            text-align: center;
            color: #95a5a6;
            font-size: 11px;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px dashed #dee2e6;
        }
        .no-data {
            text-align: center;
            padding: 50px;
            color: #95a5a6;
            font-size: 16px;
            background: white;
            border-radius: 15px;
        }
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-right: 5px;
        }
        @media print {
            .stat-card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- الهيدر -->
        <div class="header">
            <h1>📊 تقرير حالات الغش</h1>
            <p>نظام متابعة ومراقبة حالات الغش في المؤسسة التعليمية</p>
            <div class="date-badge">
                📅 الفترة: ' . htmlspecialchars($from) . ' إلى ' . htmlspecialchars($to) . '
            </div>
        </div>';

if (!empty($reports)) {
    // بطاقات الإحصائيات
    $html .= '
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-icon">📋</div>
                <div class="stat-number">' . $total . '</div>
                <div class="stat-label">إجمالي الحالات</div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-icon">⚠️</div>
                <div class="stat-number">' . $confirmed . '</div>
                <div class="stat-label">مؤكدة</div>
            </div>
            <div class="stat-card suspected">
                <div class="stat-icon">🔍</div>
                <div class="stat-number">' . $suspected . '</div>
                <div class="stat-label">مشتبه بها</div>
            </div>
            <div class="stat-card dismissed">
                <div class="stat-icon">✅</div>
                <div class="stat-number">' . $dismissed . '</div>
                <div class="stat-label">غير مثبتة</div>
            </div>
        </div>
        
        <div class="table-container">
            <div class="table-title">
                📋 قائمة تفصيلية بالحالات
            </div>
            <table>
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
                <tbody>';

    foreach ($reports as $r) {
        $statusText = [
            'confirmed' => 'مؤكدة',
            'suspected' => 'مشتبه بها',
            'dismissed' => 'غير مثبتة'
        ][$r['status']] ?? $r['status'];

        $statusClass = '';
        if ($r['status'] === 'confirmed') $statusClass = 'status-confirmed';
        elseif ($r['status'] === 'suspected') $statusClass = 'status-suspected';
        elseif ($r['status'] === 'dismissed') $statusClass = 'status-dismissed';

        $html .= '<tr>
                    <td>' . htmlspecialchars($r['name']) . '</td>
                    <td>' . $r['student_number'] . '</td>
                    <td>' . $r['major'] . '</td>
                    <td>' . $r['level'] . '</td>
                    <td>' . $r['type_name'] . '</td>
                    <td><span class="status-badge ' . $statusClass . '">' . $statusText . '</span></td>
                    <td>' . date('Y-m-d H:i', strtotime($r['event_time'])) . '</td>
                  </tr>';
    }

    $html .= '
                </tbody>
            </table>
        </div>';
} else {
    $html .= '
        <div class="no-data">
            <div style="font-size: 48px; margin-bottom: 15px;">📭</div>
            <h3>لا توجد بيانات في هذه الفترة</h3>
            <p style="margin-top: 10px;">يرجى اختيار فترة زمنية أخرى</p>
        </div>';
}

// التذييل
$html .= '
        <div class="footer">
            <p>تم إنشاء هذا التقرير بواسطة نظام إدارة التقارير - ' . date('Y-m-d H:i:s') . '</p>
            <p style="margin-top: 5px;">جميع الحقوق محفوظة © ' . date('Y') . '</p>
        </div>
    </div>
</body>
</html>';

// كتابة المحتوى
$mpdf->WriteHTML($html);

// إخراج PDF
$mpdf->Output('تقرير_حالات_الغش_' . date('Y-m-d') . '.pdf', 'I');