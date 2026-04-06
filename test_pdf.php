<?php
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
]);

$html = "
<div dir='rtl' style='text-align:right; font-family: Arial'>
    <h2>📊 تقرير نظام المراقبة</h2>
    <p><b>اسم الطالب:</b> مبارك القوينص</p>
    <p><b>رقم القيد:</b> 12345678</p>
    <p><b>نوع الغش:</b> استخدام الهاتف</p>
    <p><b>نسبة الثقة:</b> 95%</p>
</div>
";

$mpdf->WriteHTML($html);
$mpdf->Output();