<?php

include("_config.php");

$ce = new \App\CertificateHandler();

$domain = $ce->findByDomain($_GET['domain']);

if(!$domain) e404();

try {
    $ce->delete($domain);
    redirect("index.php");
} catch(\Exception $e) {
    e500($e);
}

