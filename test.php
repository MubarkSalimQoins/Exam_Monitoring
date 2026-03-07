<?php
require __DIR__ . '/vendor/autoload.php';

$pdf = new \TCPDF();  // <- backslash مهم هنا
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);
$pdf->Write(0, "اختبار TCPDF");
$pdf->Output('test.pdf', 'I');