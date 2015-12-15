<?php

error_reporting(-1);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');

require(__DIR__."/../vendor/autoload.php");

function e404()
{
    header('HTTP/1.0 404 Not Found');
    include("404.php");
    exit();
}

function e500(\Exception $e)
{
    header('HTTP/1.0 500 Internal server error');
    include("500.php");
    exit();
}

function redirect($location)
{
    header("Location: ".$location);
    exit();
}

function findLinks($text)
{
    $regex = "/((http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/";
    return preg_replace($regex, '<a href="\$1">$1</a>', $text);
}

/**
 * html escape&echo function
 * @param $string
 */
function e($string)
{
    echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * html escape&echo function
 * @param $string
 * @return string
 */
function er($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Todo: proper process management
 * @return bool
 */
function isRunning()
{
    $s = shell_exec('ps aux | grep cli.php | grep -v grep');
    return strlen($s) > 0;
}