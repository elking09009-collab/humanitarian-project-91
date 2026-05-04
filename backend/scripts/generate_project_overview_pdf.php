<?php

require __DIR__ . '/../vendor/autoload.php';

$htmlPath = dirname(__DIR__, 1) . '/../docs/project-overview-ar.html';
$pdfPath = dirname(__DIR__, 1) . '/../docs/project-overview-ar.pdf';

if (! file_exists($htmlPath)) {
    fwrite(STDERR, "HTML source not found: {$htmlPath}\n");
    exit(1);
}

$dompdf = new Dompdf\Dompdf([
    'isRemoteEnabled' => false,
    'defaultPaperSize' => 'a4',
]);

$dompdf->loadHtml(file_get_contents($htmlPath), 'UTF-8');
$dompdf->setPaper('A4');
$dompdf->render();

file_put_contents($pdfPath, $dompdf->output());

fwrite(STDOUT, "PDF_CREATED\n");