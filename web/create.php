<?php

include("_config.php");

$error = '';

// create new
if(!empty($_POST)) {
    try {
        $domain = $_POST['domain'];
        $sans = explode("\n", $_POST['san']);

        $ce = new \App\CertificateHandler();
        $ce->issueNewCertificate($domain, $sans);
        redirect("detail?domain=$domain");

    } catch(\Exception $e) {
        $error = $e->getMessage();
    }
}

$title = "Issue new certificate";
include("_header.php");

?>

<div class="head">
    <h1>Issue new certificate</h1>
    <p><a href="index">&larr; Back to the certificates list</a></p>
</div>

<div class="card">
    <form method="post" action="">

        <?php if(!empty($error)) { ?>
        <div class="error">
            <?php e($error); ?>
        </div>
        <?php } ?>

        <div class="form-row">
            <label for="domain">Base domain - Common Name (CN)</label>
            <input type="text" autofocus="autofocus" required="required" name="domain" id="domain" pattern="[a-z0-9.-]+\.[a-z]{2,63}" title="Domain name" placeholder="example.com" value="<?php echo isset($_GET['domain']) ? er($_GET['domain']) : '' ?>" />
        </div>

        <div class="form-row">
            <label for="san">Other domains - Subject Alternative Names (SAN)</label>
            <textarea name="san" id="san" placeholder="www.example.com"><?php echo isset($_GET['san']) ? er(implode("\n",explode(',',$_GET['san']))) : '' ?></textarea>
            <span class="help">(domain name per line)</span>
        </div>

        <div class="form-row">
            <button class="btn-new" type="submit">Issue new cert!</button>
        </div>

    </form>
</div>

<script>
    var sanInput = document.getElementById('san');
    var domainInput = document.getElementById('domain');

    var sanLock = false;
    if(sanInput.value.length > 0) {
        sanLock = true;
    }

    domainInput.onkeyup = function() {
        if(!sanLock) {
            sanInput.value = "www." + domainInput.value;
        }
    };
    sanInput.onkeyup = function() {
        sanLock = true;
    }
</script>


<?php
include("_footer.php");