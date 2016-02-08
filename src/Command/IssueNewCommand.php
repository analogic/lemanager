<?php

namespace App\Command;

use App\CertificateHandler;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IssueNewCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('issue:new')
            ->addOption('reuse-csr', null, InputOption::VALUE_NONE, 'Reuse CSR')
            ->addOption('domain', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Issue certificates for new request');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ch = new CertificateHandler();
        $ah = $this->getEmailAlertHandler();

        if($input->getOption('domain')) {
            $certificates = array($ch->findByDomain($input->getOption('domain')));
        } else {
            $certificates = $ch->getAll();
        }

        foreach($certificates as $certificate) {

            $logger = $this->getLogger($certificate);
            $le = $this->getLescript($logger);

            try {
                $le->initAccount();
                $le->signDomains($certificate->getAllDomains(), $input->getOption('reuse-csr'));

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