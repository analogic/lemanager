#!/usr/bin/with-contenv php
<?php

$ssl = getenv("SSL");
$hostname = getenv("HOSTNAME");

if(empty($hostname)) {
    echo "\n";
    echo "\e[41m\n";
    echo " \e[1m[ERROR]\e[21m no HOSTNAME defined, please run container with -e \"HOSTNAME=cert.example.com\"\n";
    echo "\e[49m\n";
    echo "\n";
    exit(1);
}

ob_start();
include '/etc/nginx/nginx.conf.template.php';
$output = ob_get_clean();

file_put_contents("/etc/nginx/nginx.conf", $output);
