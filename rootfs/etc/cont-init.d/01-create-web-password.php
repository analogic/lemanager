#!/usr/bin/with-contenv php
<?php

$user = getenv("HTTP_USER");
$password = getenv("HTTP_PASSWORD");

if(empty($user)) {
    $user = 'admin';
}

if(empty($password)) {
    echo "\n";
    echo "\e[41m\n";
    echo " \e[1m[ERROR]\e[21m no HTTP_PASSWORD defined, please run container with -e \"HTTP_PASSWORD=your_password\"\n";
    echo "\e[49m\n";
    echo "\n";
    exit(1);
}

# we dont need apache-utils, yay!
file_put_contents("/etc/nginx/.htpasswd", $user.":".@crypt($password));
