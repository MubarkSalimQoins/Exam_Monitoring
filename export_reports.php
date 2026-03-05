<?php
require "db.php";
require __DIR__ . '/vendor/autoload.php'; // تأكد من المسار الصحيح للمكتبات

use TCPDF\TCPDF;

// جلب البيانات
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;
$format = $_GET['format'] ?? 'pdf';

$reports = [];
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

if ($format === 'csv') {
    // تصدير CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reports.csv');
    $output = fopen('php://output', 'w');
    // العناوين
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

// إذا كان PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// إعدادات PDF
$pdf->SetCreator('نظام إدارة التقارير');
$pdf->SetAuthor('نظام إدارة الغش');
$pdf->SetTitle('تقرير حالة الغش');
$pdf->SetHeaderData('', 0, 'نتائج تقرير حالة الغش', "الفترة من: $from إلى: $to");

// خط يدعم العربية
$pdf->setHeaderFont(['dejavusans', '', 14]);
$pdf->setFooterFont(['dejavusans', '', 10]);
$pdf->SetMargins(10, 20, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 10);
$pdf->setFontSubsetting(true);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);

// HTML الجدول
$html = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; text-align: center;">';
$html .= '<thead style="background-color:#f1f1f1;">
            <tr>
                <th>الطالب</th>
                <th>رقم القيد</th>
                <th>التخصص</th>
                <th>المستوى</th>
                <th>نوع الغش</th>
                <th>الحالة</th>
                <th>التاريخ</th>
            </tr>
          </thead><tbody>';

foreach ($reports as $r) {
    $statusColor = $r['status'] === 'confirmed' ? '#ff4d4d' : ($r['status'] === 'suspected' ? '#ffd633' : '#66cc66');
    $html .= '<tr>
                <td>'.htmlspecialchars($r['name']).'</td>
                <td>'.$r['student_number'].'</td>
                <td>'.$r['major'].'</td>
                <td>'.$r['level'].'</td>
                <td>'.$r['type_name'].'</td>
                <td style="background-color: '.$statusColor.';">'.$r['status'].'</td>
                <td>'.$r['event_time'].'</td>
              </tr>';
}

$html .= '</tbody></table>';

// إخراج PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('report.pdf', 'I');