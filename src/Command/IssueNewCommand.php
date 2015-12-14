<?php

namespace App\Command;

use App\CertificateHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IssueNewCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('issue:new')
            ->setDescription('Issue certificates for new request');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ch = new CertificateHandler();
        $ah = $this->getEmailAlertHandler();

        foreach($ch->getAll() as $certificate) {
            if(!$certificate->isPending()) continue;

            $logger = $this->getLogger($certificate);
            $le = $this->getLescript($logger);

            try {
                $le->initAccount();
                $le->signDomains($certificate->getAllDomains());

                $ah->sendIssuedLog($certificate);

            } catch(\Exception $e) {

                $logger->error($e->getMessage());
                foreach(explode("\n", $e->getTraceAsString()) as $line) {
                    $logger->debug($line);
                }

                $ah->sendErrorLog($certificate);
            }
        }
    }
}