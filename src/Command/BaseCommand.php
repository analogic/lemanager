<?php
/**
 * © 2015 Analogic s.r.o. info@analogic.cz
 */

namespace App\Command;

use Analogic\ACME\Lescript;
use App\Certificate;
use App\Configuration\Email;
use App\EmailAlertHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Yaml;

abstract class BaseCommand extends Command
{
    protected function getLogger(Certificate $certificate)
    {
        $logPath = $certificate->getPath().'/last.log';
        @unlink($logPath);
        $logger = new Logger('issue-new');
        $handler = new StreamHandler($logPath);
        $handler->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
        $logger->pushHandler($handler);

        return $logger;
    }

    protected function getEmailAlertHandler()
    {
        $config = new Email();

        if(is_file("/data/smtp.yml")) {
            $config->loadConfig(Yaml::parse(file_get_contents('/data/smtp.yml')));
        }

        return new EmailAlertHandler($config);
    }

    protected function getLescript(Logger $logger)
    {
        return new Lescript('/data', "/opt/lemanager/web/", $logger);
    }
}