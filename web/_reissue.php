<?php

include("_config.php");

$ce = new \App\CertificateHandler();

$domain = $ce->findByDomain($_GET['domain']);
if(!$domain) e404();

try {

    $ce = new \App\CertificateHandler();
    $ce->issueNewCertificate($domain->getName(), $domain->getSAN(), true);
    redirect("detail?domain=".$domain->getName());

} catch(\Exception $e) {
    $error = $e->getMessage();
    include "detail.php";
}
