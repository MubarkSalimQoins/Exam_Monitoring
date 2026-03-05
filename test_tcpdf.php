<?php
require __DIR__ . '/vendor/autoload.php';

// استخدام كلاس TCPDF مباشرة
$pdf = new \TCPDF();
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);
$pdf->Write(0, "اختبار TCPDF");
$pdf->Output('test.pdf', 'I');س