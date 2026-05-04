<?php

require __DIR__ . '/../vendor/autoload.php';

// Manual autoloader for mPDF (not in composer autoload_static)
spl_autoload_register(function (string $class): void {
    if (strncmp($class, 'Mpdf\\', 5) !== 0) {
        return;
    }
    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, 5));
    $file = __DIR__ . '/../vendor/mpdf/mpdf/src/' . $relative . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
}, true, true); // prepend = true so it runs before composer

// mPDF helper functions
$helpersFile = __DIR__ . '/../vendor/mpdf/mpdf/src/functions.php';
if (file_exists($helpersFile)) {
    require_once $helpersFile;
}

$htmlPath = dirname(__DIR__, 2) . '/docs/project-overview-ar.html';
$pdfPath  = dirname(__DIR__, 2) . '/docs/project-overview-ar.pdf';

if (! file_exists($htmlPath)) {
    fwrite(STDERR, "HTML source not found: {$htmlPath}\n");
    exit(1);
}

$html = file_get_contents($htmlPath);

$mpdf = new \Mpdf\Mpdf([
    'mode'          => 'utf-8',
    'format'        => 'A4',
    'direction'     => 'rtl',
    'default_font'  => 'dejavusans',
    'autoScriptToLang'  => true,
    'autoLangToFont'    => true,
]);

$mpdf->WriteHTML($html);
$mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

fwrite(STDOUT, "PDF_CREATED\n");
