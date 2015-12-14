<?php

namespace App;

class Certificate {

    private $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getPath()
    {
        return "/data/".$this->name;
    }

    public function getSAN()
    {
        return explode("\n", file_get_contents($this->getPath()."/domains"));
    }

    public function getAllDomains()
    {
        $domains = [$this->getName()];
        $domains = array_merge($domains, $this->getSAN());
        return array_unique($domains);
    }

    public function getExpirationDate()
    {
        if(!is_file($this->getPath()."/cert.pem")) {
            return null;
        }
        $data = openssl_x509_parse(file_get_contents($this->getPath()."/cert.pem"));

        $dt = new \DateTime();
        $dt->setTimestamp($data["validTo_time_t"]);
        return $dt;
    }

    public function getExpirationInterval()
    {
        function pluralize( $count, $text ) {
            return $count . ( ( $count == 1 ) ? ( " $text" ) : ( " ${text}s" ) );
        }

        $interval = (new \DateTime('now'))->diff($this->getExpirationDate());

        $string = '';

        if ( $v = $interval->m >= 1 ) $string .= pluralize( $interval->m, 'month' ). ' ';
        if ( $v = $interval->d >= 1 ) $string .= pluralize( $interval->d, 'day' );
        $string .= ($interval->invert ? ' ago' : '' );

        return $string;
    }

    public function hasLastLog()
    {
        return is_readable($this->getPath()."/last.log");
    }

    public function getLastLog()
    {
        return file_get_contents($this->getPath()."/last.log");
    }

    public function listCertificateFiles()
    {
        return glob($this->getPath()."/*.pem");
    }

    public function showCertificateFile($name)
    {
        $name = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $name);
        $path = $this->getPath()."/".$name;

        if(!is_readable($path)) throw new \RuntimeException('File not available');
        return file_get_contents($path);
    }

    public function getName()
    {
        return $this->name;
    }

    public function isExpiringOrInvalid()
    {
        $ed = $this->getExpirationDate();
        if(!$ed) {
            // invalid
            return true;
        }
        $interval = (new \DateTime('now'))->diff($ed);

        // LE certificates gets 90 days to expiry

        return $interval->d < 15 || $interval->m < 2;
    }

    public function isPending()
    {
        return !is_file($this->getPath()."/cert.pem");
    }

    public function getIssuedDetails()
    {
        $cert = file_get_contents($this->getPath()."/cert.pem");
        $details = openssl_x509_parse($cert);

        // filter only active purposes and show names only
        $details['purposes'] =
            array_map(
                    function($item) { return $item[2]; },
                    array_filter($details['purposes'], function($item) { return $item[0] == 1; })
            );

        return json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}