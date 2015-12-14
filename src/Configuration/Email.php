<?php

namespace App\Configuration;

class Email
{
    /** @var string */
    public $smtpHost;

    /** @var integer */
    public $smtpPort;

    /** @var string */
    public $smtpEncryption;

    /** @var string */
    public $smtpUser;

    /** @var string */
    public $smtpPassword;

    /** @var string */
    public $targetEmail;

    /** @var bool */
    public $renewEmails;

    /** @var bool */
    public $errorEmails;


    public function __construct(
        $alertEmailTarget = '',
        $alertEmailSource = 'lemanager-robot@localhost',
        $smtpHost = '127.0.0.1',
        $smtpPort = '25',
        $smtpEncryption = '',
        $smtpUser = '',
        $smtpPassword = '',

        $alertError = true,
        $alertRenew = true,
        $alertIssued = true
    ) {
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->smtpEncryption = $smtpEncryption;
        $this->smtpUser = $smtpUser;
        $this->smtpPassword = $smtpPassword;

        $this->alertEmailSource = $alertEmailSource;
        $this->alertEmailTarget = $alertEmailTarget;

        $this->alertError = $alertError;
        $this->alertRenew = $alertRenew;
        $this->alertIssued = $alertIssued;
    }

    public function loadConfig(array $config)
    {
        if(isset($config['smtpHost'])) $this->smtpHost = $config['smtpHost'];
        if(isset($config['smtpPort'])) $this->smtpPort = $config['smtpPort'];
        if(isset($config['smtpEncryption'])) $this->smtpEncryption = $config['smtpEncryption'];
        if(isset($config['smtpUser'])) $this->smtpUser = $config['smtpUser'];
        if(isset($config['smtpPassword'])) $this->smtpPassword = $config['smtpPassword'];

        if(isset($config['alertEmailSource'])) $this->alertEmailSource = $config['alertEmailSource'];
        if(isset($config['alertEmailTarget'])) $this->alertEmailTarget = $config['alertEmailTarget'];
        $this->alertError = isset($config['alertError']);
        $this->alertRenew = isset($config['alertRenew']);
        $this->alertIssued = isset($config['alertIssued']);
    }
    
    public function export()
    {
        return array(
            'smtpHost' => $this->smtpHost,
            'smtpPort' => $this->smtpPort,
            'smtpEncryption' => $this->smtpEncryption,
            'smtpUser' => $this->smtpUser,
            'smtpPassword' => $this->smtpPassword,
    
            'alertEmailSource' => $this->alertEmailSource,
            'alertEmailTarget' => $this->alertEmailTarget,
            'alertError' => $this->alertError,
            'alertRenew' => $this->alertRenew,
            'alertIssued' => $this->alertIssued
        );
    }
}