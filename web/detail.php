<?php

include("_config.php");

$ch = new \App\CertificateHandler();

$certificate = $ch->findByDomain($_GET['domain']);
if(!$certificate) e404();

$title = "Certificate ".$certificate->getName();
include("_header.php");

?>

<div class="head">
    <h1>Certificate for <?php e($certificate->getName()) ?></h1>
    <p><a href="index">&larr; Back to the certificates list</a></p>
</div>

<?php if(!empty($error)) { ?>
    <div class="error">
        <?php e($error); ?>
    </div>
<?php } ?>

<?php if(isRunning()) { ?>
    <div class="running">
        <div class="loader"></div> <span style="padding-left: 1.5em;">Worker script is running, please wait for results. <a style="text-decoration: underline" href="detail?domain=<?php e($certificate->getName()) ?>">Refresh</a>.</span>
    </div>
<?php } ?>

<div class="card">
    <h3>Domains</h3>
    <ul>
        <?php foreach($certificate->getSAN() as $domain) { ?>
        <li><?php e($domain) ?></li>
        <?php } ?>
    </ul>


    <a href="_reissue?domain=<?php e($domain) ?>" onclick="confirm('Are you sure?')" style="margin-top: 1em;" class="btn btn-new">Try to reissue certificate</a>

    <a href="create.php?domain=<?php e($domain) ?>&san=<?php e(join(',', $certificate->getSAN())); ?>" style="margin-top: 1em;" class="btn btn-new">Add/Change domains</a>
</div>

<?php if(count($certificate->listCertificateFiles()) > 0) { ?>
<div class="card">
    <h3>Certificate files</h3>
    <ul>
        <?php foreach($certificate->listCertificateFiles() as $file) { ?>
            <li><a href="_content?domain=<?php e($certificate->getName()) ?>&file=<?php e(basename($file)) ?>"><?php e(basename($file)) ?></a></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>

<?php if(!$certificate->isPending()) { ?>
    <div class="card">
        <h3>Issued certificate details (cert.pem)</h3>
        <pre><code><?php e($certificate->getIssuedDetails()) ?></code></pre>
    </div>


    <?php if($certificate->hasLastLog()) { ?>
        <div class="card">
            <h3>Last log</h3>
            <pre><code><?php e($certificate->getLastLog()) ?></code></pre>
        </div>
    <?php } ?>

<?php } else { ?>

    <?php if($certificate->hasLastLog()) { ?>
        <div class="card">
            <h3>Last log</h3>
            <pre><code><?php
            foreach(explode("\n", $certificate->getLastLog()) as $line) {
                if(preg_match('~ERROR:~', $line)) {
                    echo '<strong>';
                    echo findLinks(er($line));
                    echo "</strong>\n";
                } else if (preg_match('~DEBUG:~', $line)) {
                    echo '<span style="color: gray;">';
                    echo findLinks(er($line));
                    echo "</span>\n";
                } else {
                    echo er($line);
                    echo "\n";
                }
            }
            ?></code></pre>
        </div>
    <?php } ?>

    <div class="card">
        <h3>Issued certificate details</h3>
        <p><em>certificate was not yet issued, please wait...</em></p>
    </div>
<?php } ?>

<?php
include("_footer.php");