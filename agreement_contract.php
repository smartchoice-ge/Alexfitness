<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/tcpdf_loader.php';

try {
    if (!loadTcpdfLibrary(__DIR__)) {
        http_response_code(500);
        echo 'PDF generator is unavailable.';
        exit;
    }

    date_default_timezone_set('Asia/Tbilisi');

    $year = date('Y');
    $full_name = '____________________________';
    $id_number = '____________________________';
    $mobile_number = '+995 ____________________';
    $email = '____________________________';

    ob_start();
    include __DIR__ . '/agreement_template.php';
    $html_content = ob_get_clean();

    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Alex Fitness');
    $pdf->SetTitle('Alex Fitness Agreement');
    $pdf->SetSubject('Public membership agreement');
    $pdf->SetKeywords('TCPDF, PDF, agreement, alex fitness');
    $pdf->SetMargins(12, 12, 12);
    $pdf->SetAutoPageBreak(true, 12);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->writeHTML($html_content, true, false, true, false, '');

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="Alex-Fitness-agreement.pdf"');

    $pdf->Output('Alex-Fitness-agreement.pdf', 'I');
    exit;
} catch (Throwable $e) {
    error_log('Public agreement PDF failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Failed to load contract.';
}