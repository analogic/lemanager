<?php

namespace App;

use App\Configuration\Email;

class EmailAlertHandler
{
    public $config;

    private $append = "<br />\n<br />\n<br />\n<em>(if you don't want receive this emails, please adjust settings at LEManager)</em>";

    public function __construct(Email $config)
    {
        $this->config = $config;
    }

    public function sendErrorLog(Certificate $certificate)
    {
        if(!$this->config->alertError) return false;

        $subject = "Certificate ".$certificate->getName()." issuance error";
        $body =
            "Hello,<br />\n".
            " there happened error when script was issuing certificate ".$certificate->getName().":<br />\n<br />\n".
            nl2br($certificate->getLastLog()).
            $this->append;

        return $this->email($subject, $body);
    }

    public function sendIssuedLog(Certificate $certificate)
    {
        if(!$this->config->alertIssued) return false;

        $subject = "Certificate ".$certificate->getName()." was issued";
        $body =
            "Hello,<br />\n".
            " good news everyone! Certificate for ".$certificate->getName()." was issued:<br />\n<br />\n".
            nl2br($certificate->getLastLog()).
            $this->append;

        return $this->email($subject, $body);
    }

    public function sendRenewLog(Certificate $certificate)
    {
        if(!$this->config->alertRenew) return false;

        $subject = "Certificate ".$certificate->getName()." was renewed";
        $body =
            "Hello,<br />\n".
            " good news everyone! Certificate for ".$certificate->getName()." was renewed:<br />\n<br />\n".
            nl2br($certificate->getLastLog()).
            $this->append;

        return $this->email($subject, $body);
    }

    public function sendTestMessage()
    {
        $subject = "Test message from LEManager";
        $body =
            "Hello,<br />\n this is test message!";

        return $this->email($subject, $body);
    }

    private function email($subject, $body)
    {
        $transport = \Swift_SmtpTransport::newInstance($this->config->smtpHost, $this->config->smtpPort, $this->config->smtpEncryption);

        if(!empty($this->config->smtpUser)) {
            $transport
                ->setAuthMode('login')
                ->setUsername($this->config->smtpUser)
                ->setPassword($this->config->smtpPassword);
        }
        $transport->setStreamOptions(array('ssl' => array('verify_peer' => false)));

        $mailer = \Swift_Mailer::newInstance($transport);
        $message = \Swift_Message::newInstance($subject)
            ->setFrom(array($this->config->alertEmailSource))
            ->setTo(array($this->config->alertEmailTarget))
            ->setBody(strip_tags($body))
            ->addPart($body, 'text/html');


        return $mailer->send($message);
    }
}