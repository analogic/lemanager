<?php

include("_config.php");

use \Symfony\Component\Yaml\Yaml;

$config = new \App\Configuration\Email();

if(is_file(DATA_DIR . "smtp.yml")) {
    $config->loadConfig(Yaml::parse(file_get_contents(DATA_DIR . 'smtp.yml')));
}

if(!empty($_POST)) {
    $config->loadConfig($_POST);
    file_put_contents(DATA_DIR . 'smtp.yml', Yaml::dump($config->export()));
    $em = new \App\EmailAlertHandler($config);
    try {
        $em->sendTestMessage();
        $message = "Changes saved, test message was sent to ${_POST['alertEmailTarget']}!";
    } catch(\Exception $e) {
        $error = $e->getMessage();
    }
}


$title = "Email alerts";
include("_header.php");

?>

    <div class="head">
        <h1><?php echo $title ?></h1>
        <p><a href="index">&larr; Back to the certificates list</a></p>
    </div>

    <div class="card">

        <form method="post" action="">

            <?php if(!empty($message)) { ?>
                <div class="notice">
                    <?php e($message) ?>
                </div>
            <?php } ?>

            <?php if(!empty($error)) { ?>
                <div class="error">
                    <?php e($error); ?>
                </div>
            <?php } ?>

            <div class="form-row">
                <label for="alertEmailTarget">Alerts destination email</label>
                <input type="email" autofocus="autofocus" required="required" name="alertEmailTarget" id="alertEmailTarget" value="<?php e($config->alertEmailTarget) ?>" placeholder="joe@example.com" />
            </div>

            <div class="form-row">
                <label for="alertEmailSource">Alerts source email</label>
                <input type="email" required="required" name="alertEmailSource" id="alertEmailSource" value="<?php e($config->alertEmailSource) ?>" placeholder="lemanager-robot@example.com" />
            </div>

            <div class="form-row">
                <label for="domain">SMTP server</label>
                <input type="text" required="required" name="smtpHost" id="smtpHost" title="SMTP server domain" value="<?php e($config->smtpHost) ?>" placeholder="mail.example.com" />
            </div>

            <div class="form-row">
                <label for="san">SMTP port</label>
                <input type="number" placeholder="25" value="<?php e($config->smtpPort) ?>" name="smtpPort" id="smtpPort">
            </div>

            <div class="form-row">
                <label for="smtpEncryption">SMTP encryption</label>
                <select name="smtpEncryption" id="smtpEncryption">
                    <option value="">(none)</option>
                    <option <?php if($config->smtpEncryption == 'ssl') { ?>selected="selected"<?php } ?> value="ssl">SSL</option>
                    <option <?php if($config->smtpEncryption == 'tls') { ?>selected="selected"<?php } ?> value="tls">TLS</option>
                </select>
            </div>

            <div class="form-row">
                <label for="smtpUser">SMTP username</label>
                <input type="text" placeholder="joe" value="<?php e($config->smtpUser) ?>" name="smtpUser" id="smtpUser">
            </div>

            <div class="form-row">
                <label for="smtpPassword">SMTP password</label>
                <input type="password" value="<?php e($config->smtpPassword) ?>" name="smtpPassword" id="smtpPassword">
            </div>

            <div class="form-row">
                <label class="checkbox"><input type="checkbox" name="alertError" <?php if($config->alertError) { ?>checked="checked"<?php } ?>/> Send email when <strong>error</strong> happen</label>
            </div>

            <div class="form-row">
                <label class="checkbox"><input type="checkbox" name="alertRenew" <?php if($config->alertRenew) { ?>checked="checked"<?php } ?>/> Send email when certificate was <strong>renewed</strong></label>
            </div>

            <div class="form-row">
                <label class="checkbox"><input type="checkbox" name="alertIssued" <?php if($config->alertIssued) { ?>checked="checked"<?php } ?>/> Send email when new certificate was <strong>issued</strong></label>
            </div>


            <div class="form-row">
                <button class="btn-show" type="submit">Send me test message and save</button>
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
?>
