<?php
/**
 * © 2015 Analogic s.r.o. info@analogic.cz
 */

namespace App\Command;

require_once("_config.php");

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

        if(is_file(DATA_DIR . "smtp.yml")) {
            $config->loadConfig(Yaml::parse(file_get_contents(DATA_DIR . 'smtp.yml')));
        }

        return new EmailAlertHandler($config);
    }

    protected function getLescript(Logger $logger)
    {
        return new Lescript(DATA_DIR . '', dirname(dirname(__DIR__)) . "/web", $logger);
    }
}
