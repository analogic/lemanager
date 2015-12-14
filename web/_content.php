<?php

include("_config.php");

$ce = new \App\CertificateHandler();

$certificate = $ce->findByDomain($_GET['domain']);
if(!$certificate) e404();

try {
    header("Content-Type: text/plain");
    echo $certificate->showCertificateFile($_GET['file']);
    exit();
} catch(\Exception $e) {
    e500($e);
}

