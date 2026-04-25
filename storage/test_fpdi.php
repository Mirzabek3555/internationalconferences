<?php
require 'vendor/autoload.php';
use setasign\Fpdi\Tcpdf\Fpdi;

$errorStr = '';
try {
    $pdf = new Fpdi('P', 'mm', 'A4');
    $pdf->setSourceFile('storage/app/public/articles/base/docx_base_1776661250_227.pdf');
    echo "Success parsing docx_base_1776661250_227.pdf\n";
} catch (\Throwable $e) {
    echo "Error parsing: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
