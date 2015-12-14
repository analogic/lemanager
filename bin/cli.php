<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$console = new Application('LEManager', 'Beta');

$console->add(new App\Command\RenewAllCommand());
$console->add(new App\Command\IssueNewCommand());

$console->run();