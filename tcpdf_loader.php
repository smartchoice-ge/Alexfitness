<?php

function loadTcpdfLibrary(string $baseDir): bool
{
    if (class_exists('TCPDF', false)) {
        return true;
    }

    $autoloadPath = $baseDir . '/vendor/autoload.php';
    if (is_file($autoloadPath)) {
        require_once $autoloadPath;
    }

    if (class_exists('TCPDF', false)) {
        return true;
    }

    $tcpdfPath = $baseDir . '/vendor/tecnickcom/tcpdf/tcpdf.php';
    if (is_file($tcpdfPath)) {
        require_once $tcpdfPath;
    }

    return class_exists('TCPDF', false);
}
