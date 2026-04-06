<?php
require "db.php";
require __DIR__ . '/vendor/autoload.php';
use Mpdf\Mpdf;

$from   = $_GET['from']   ?? null;
$to     = $_GET['to']     ?? null;
$format = $_GET['format'] ?? 'pdf';

$reports   = [];
$total     = 0;
$confirmed = 0;
$suspected = 0;
$dismissed = 0;

if ($from && $to) {
    $sql = "SELECT
        s.name, s.student_number, s.major, s.level,
        ct.type_name, ce.status, ce.event_time
    FROM cheating_events ce
    JOIN students s      ON ce.student_id       = s.student_id
    JOIN cheating_types ct ON ce.cheating_type_id = ct.cheating_type_id
    WHERE DATE(ce.event_time) BETWEEN :from AND :to
    ORDER BY ce.event_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':from' => $from, ':to' => $to]);
    $reports   = $stmt->fetchAll();
    $total     = count($reports);
    $confirmed = count(array_filter($reports, fn($r) => $r['status'] === 'confirmed'));
    $suspected = count(array_filter($reports, fn($r) => $r['status'] === 'suspected'));
    $dismissed = count(array_filter($reports, fn($r) => $r['status'] === 'dismissed'));
}

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=reports.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    fputcsv($output, ['الطالب','رقم القيد','التخصص','المستوى','نوع الغش','الحالة','التاريخ']);
    foreach ($reports as $r) {
        fputcsv($output, [$r['name'],$r['student_number'],$r['major'],$r['level'],$r['type_name'],$r['status'],$r['event_time']]);
    }
    fclose($output);
    exit;
}

// ===== إعداد mPDF مع خط عربي صحيح =====
$mpdf = new Mpdf([
    'mode'              => 'utf-8',
    'format'            => 'A4',
    'margin_left'       => 10,
    'margin_right'      => 10,
    'margin_top'        => 6,
    'margin_bottom'     => 12,
    'directionality'    => 'rtl',
    'default_font'      => 'dejavusans',
    'autoScriptToLang'  => true,
    'autoLangToFont'    => true,
    'autoArabic'        => true,
]);

$mpdf->SetCreator('جامعة شبوة');
$mpdf->SetAuthor('نظام مراقبة الاختبارات');
$mpdf->SetTitle('تقرير حالات الغش');

$generatedAt = date('Y/m/d  H:i');
$periodText  = htmlspecialchars($from ?? '') . ' — ' . htmlspecialchars($to ?? '');

// ===== صفوف الجدول =====
$tableRows = '';
if (!empty($reports)) {
    foreach ($reports as $i => $r) {
        $statusMap = [
            'confirmed' => ['label' => 'مؤكدة',     'bg' => '#dc2626', 'c' => '#fff'],
            'suspected' => ['label' => 'مشتبه بها', 'bg' => '#d97706', 'c' => '#fff'],
            'dismissed' => ['label' => 'غير مثبتة', 'bg' => '#16a34a', 'c' => '#fff'],
        ];
        $st    = $statusMap[$r['status']] ?? ['label' => $r['status'], 'bg' => '#64748b', 'c' => '#fff'];
        $rowBg = ($i % 2 === 0) ? '#ffffff' : '#f0f5ff';
        $date  = date('Y-m-d', strtotime($r['event_time']));
        $time  = date('H:i',   strtotime($r['event_time']));

        $tableRows .= '
        <tr style="background:' . $rowBg . ';">
            <td class="tc gray">' . ($i + 1) . '</td>
            <td class="bold dark">' . htmlspecialchars($r['name']) . '</td>
            <td class="tc mid">' . htmlspecialchars($r['student_number']) . '</td>
            <td class="tc mid">' . htmlspecialchars($r['major']) . '</td>
            <td class="tc mid">' . htmlspecialchars($r['level']) . '</td>
            <td class="tc mid">' . htmlspecialchars($r['type_name']) . '</td>
            <td class="tc">
                <span style="background:' . $st['bg'] . ';color:' . $st['c'] . ';padding:3px 9px;border-radius:10px;font-size:11px;font-weight:bold;">' . $st['label'] . '</span>
            </td>
            <td class="tc gray" style="font-size:11px;">' . $date . '<br>' . $time . '</td>
        </tr>';
    }
} else {
    $tableRows = '<tr><td colspan="8" style="text-align:center;padding:35px;color:#94a3b8;font-size:13px;">لا توجد بيانات في هذه الفترة الزمنية</td></tr>';
}

$html = '
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<style>

* {
    font-family: "dejavusans", sans-serif;
    font-size: 14px;
    line-height: 1.8;
}

body { margin:0; padding:0; direction:rtl; background:#fff; color:#1e293b; }

/* ===== HEADER ===== */
.hdr-outer {
    background: #1e3a8a;
    padding: 0;
    margin-bottom: 14px;
}

.hdr-top {
    background: #1e40af;
    padding: 16px 18px 12px;
}

.hdr-tbl { width:100%; border-collapse:collapse; }
.hdr-tbl td { vertical-align:middle; padding:0; }

.hdr-ar {
    font-size: 16px;
    font-weight: bold;
    color: #ffffff;
    line-height: 1.8;
}
.hdr-ar small { display:block; font-size:11px; color:#93c5fd; font-weight:normal; }

.hdr-en {
    font-size: 14px;
    font-weight: bold;
    color: #ffffff;
    text-align: left;
    direction: ltr;
    line-height: 1.8;
}
.hdr-en small { display:block; font-size:10px; color:#93c5fd; font-weight:normal; }

.logo-td { text-align:center; width:80px; }

.hdr-bot {
    background: #172554;
    padding: 8px 18px;
    text-align: center;
    border-top: 2px solid #3b82f6;
}
.hdr-bot .title { font-size:17px; font-weight:bold; color:#ffffff; letter-spacing:0.5px; }
.hdr-bot .sub   { font-size:10px; color:#93c5fd; margin-top:2px; }

/* ===== META BAR ===== */
.meta {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    padding: 8px 14px;
    margin-bottom: 14px;
}
.meta table { width:100%; border-collapse:collapse; }
.meta td { font-size:12px; padding:2px 4px; vertical-align:middle; }
.meta .lbl { color:#1e40af; font-weight:bold; width:110px; }
.meta .val { color:#1e293b; }

/* ===== STATS ===== */
.stats { margin-bottom:14px; }
.stats table { width:100%; border-collapse:separate; border-spacing:6px; }
.stats td { width:25%; vertical-align:top; padding:0; }

.sc {
    border-radius: 8px;
    padding: 12px 8px;
    text-align: center;
    border: 1px solid;
}
.sc.bl { background:#eff6ff; border-color:#bfdbfe; }
.sc.rd { background:#fef2f2; border-color:#fecaca; }
.sc.yw { background:#fffbeb; border-color:#fde68a; }
.sc.gn { background:#f0fdf4; border-color:#bbf7d0; }

.sc-num { font-size:28px; font-weight:bold; line-height:1.1; margin-bottom:3px; }
.sc.bl .sc-num { color:#1d4ed8; }
.sc.rd .sc-num { color:#dc2626; }
.sc.yw .sc-num { color:#d97706; }
.sc.gn .sc-num { color:#16a34a; }

.sc-lbl { font-size:12px; color:#64748b; font-weight:bold; }

/* ===== DIVIDER ===== */
.div {
    height: 2px;
    background: #1e40af;
    margin: 12px 0;
    border-radius: 2px;
}

/* ===== SECTION TITLE ===== */
.sec-title {
    font-size: 14px;
    font-weight: bold;
    color: #1e3a8a;
    margin-bottom: 8px;
    padding-right: 10px;
    border-right: 4px solid #3b82f6;
    line-height: 1.6;
}

/* ===== TABLE ===== */
.dt { width:100%; border-collapse:collapse; font-size:12px; }

.dt thead tr { background:#1e40af; }
.dt thead th {
    color: #ffffff;
    padding: 10px 7px;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    border: none;
}

.dt tbody td {
    padding: 8px 7px;
    border-bottom: 1px solid #e2e8f0;
    font-size: 12px;
}
.dt tbody tr:last-child td { border-bottom: none; }

.tc   { text-align: center; }
.bold { font-weight: bold; }
.dark { color: #1e293b; }
.mid  { color: #475569; }
.gray { color: #64748b; }

/* ===== FOOTER ===== */
.ftr {
    margin-top: 16px;
    padding-top: 10px;
    border-top: 2px solid #e2e8f0;
    text-align: center;
}
.ftr-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 9px 14px;
}
.ftr p { margin:2px 0; font-size:10px; color:#94a3b8; }
.ftr .fm { font-size:11px; color:#64748b; font-weight:bold; }

</style>
</head>
<body>

<!-- HEADER -->
<div class="hdr-outer">
    <div class="hdr-top">
        <table class="hdr-tbl">
            <tr>
                <td style="width:38%;">
                    <div class="hdr-ar">
                        تقارير حالات الغش
                        <small>نظام مراقبة الاختبارات</small>
                    </div>
                </td>
                <td class="logo-td">
                    <img src="' . __DIR__ . '/images/shbwoh.jpg" width="70" height="70" style="border-radius:50%;border:3px solid #60a5fa;">
                </td>
                <td style="width:38%;">
                    <div class="hdr-en">
                        Cheating Cases Report
                        <small>Exam Monitoring System</small>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="hdr-bot">
        <div class="title">جامعة شبوة &nbsp;|&nbsp; Shabwa University</div>
        <div class="sub">كلية تقنية المعلومات — College of Information Technology</div>
    </div>
</div>

<!-- META BAR -->
<div class="meta">
    <table>
        <tr>
            <td class="lbl">الفترة الزمنية:</td>
            <td class="val">' . $periodText . '</td>
            <td style="width:20px;"></td>
            <td class="lbl">تاريخ الإصدار:</td>
            <td class="val">' . $generatedAt . '</td>
            <td style="width:20px;"></td>
            <td class="lbl">إجمالي الحالات:</td>
            <td class="val" style="font-weight:bold;color:#1d4ed8;">' . $total . ' حالة</td>
        </tr>
    </table>
</div>

<!-- STATS -->
<div class="stats">
    <table>
        <tr>
            <td><div class="sc bl"><div class="sc-num">' . $total . '</div><div class="sc-lbl">إجمالي الحالات</div></div></td>
            <td><div class="sc rd"><div class="sc-num">' . $confirmed . '</div><div class="sc-lbl">حالات مؤكدة</div></div></td>
            <td><div class="sc yw"><div class="sc-num">' . $suspected . '</div><div class="sc-lbl">مشتبه بها</div></div></td>
            <td><div class="sc gn"><div class="sc-num">' . $dismissed . '</div><div class="sc-lbl">غير مثبتة</div></div></td>
        </tr>
    </table>
</div>

<div class="div"></div>

<!-- TABLE -->
<div class="sec-title">قائمة تفصيلية بحالات الغش المسجلة</div>

<table class="dt">
    <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th style="width:19%;">اسم الطالب</th>
            <th style="width:11%;">رقم القيد</th>
            <th style="width:11%;">التخصص</th>
            <th style="width:8%;">المستوى</th>
            <th style="width:16%;">نوع الغش</th>
            <th style="width:12%;">الحالة</th>
            <th style="width:14%;">التاريخ</th>
        </tr>
    </thead>
    <tbody>' . $tableRows . '</tbody>
</table>

<div class="div"></div>

<!-- FOOTER -->
<div class="ftr">
    <div class="ftr-box">
        <p class="fm">جامعة شبوة — Shabwa University — نظام مراقبة الاختبارات</p>
        <p>تم إنشاء هذا التقرير آلياً بتاريخ: ' . $generatedAt . ' &nbsp;|&nbsp; هذا التقرير سري ولا يجوز تداوله</p>
        <p>Automatically generated by Exam Monitoring System &copy; ' . date('Y') . '</p>
    </div>
</div>

</body>
</html>';

$mpdf->WriteHTML($html);
$mpdf->Output('تقرير_حالات_الغش_' . date('Y-m-d') . '.pdf', 'I');
