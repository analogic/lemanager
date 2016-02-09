<?php

namespace App;

require_once("_config.php");

class CertificateHandler
{
    public function issueNewCertificate($cn, array $san, $reuseCSR = false)
    {
        $cn = trim($cn);
        $san[] = $cn;
        $san = array_unique($san);
        $san = array_map(function($domain) { return trim(mb_strtolower($domain)); }, $san);

        foreach($san as $key => $domain) {
            if(empty($domain)) {
                unset($san['key']);
                continue;
            }
            if(!$this->validateDomain($domain)) {
                throw new \RuntimeException("Domain name \"$domain\" is not valid");
            }
        }

        @mkdir(DATA_DIR . "$cn");
        $path = DATA_DIR . "$cn/domains";

        @file_put_contents($path, join("\n", $san));
        if(!@is_file($path)) {
            throw new \RuntimeException("Can't create file at path $path");
        }

        // TODO: rewrite, good enough for now
        exec('/usr/bin/php '.__DIR__."/../bin/cli.php issue:new --domain $cn ".($reuseCSR ? '--reuse-csr' : '')." > /dev/null 2>&1 &");
    }

    private function validateDomain($domain)
    {
        return preg_match('~^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$~i', $domain);
    }

    /**
     * @return Certificate[]
     */
    public function getAll()
    {
        $certificates = [];

        foreach(glob(DATA_DIR . '*') as $path) {
            if(!is_dir($path) || basename($path) == "_account") continue;

            $name = basename($path);
            $certificates[] = new Certificate($name);
        }

        return $certificates;
    }

    /**
     * @param string $domain
     * @return Certificate
     */
    public function findByDomain($domain)
    {
        if(!is_dir(DATA_DIR . $domain)) {
            return null;
        }
        return new Certificate($domain);
    }

    /**
     * @param Certificate $certificate
     * @return bool
     */
    public function delete(Certificate $certificate)
    {
        return $this->rmrf($certificate->getPath());
    }

    /**
     * @param string $target
     * @return bool
     */
    private function rmrf($target) {
        $files = array_diff(scandir($target), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$target/$file") && !is_link($target)) ? $this->rmrf("$target/$file") : unlink("$target/$file");
        }
        return rmdir($target);
    }
}
