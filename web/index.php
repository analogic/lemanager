<?php

include("_config.php");

$ch = new \App\CertificateHandler();

$title = "Certificates list";
include("_header.php");
?>

<div class="head">
    <h1>Certificates list</h1>
    <p><a href="create" class="btn-new">Issue new certificate</a></p>
</div>

<table>
    <thead>
    <tr>
        <th>Domain (CN)</th>
        <th>Other domains (SAN)</th>
        <th>Expiration</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($ch->getAll() as $certificate) { ?>
    <tr>
        <th>
            <a href="detail?domain=<?php e($certificate->getName()) ?>">
                <?php e($certificate->getName()) ?>
            </a>
        </th>
        <td><?php e(mb_strimwidth(join(", ", $certificate->getSAN()), 0, 40, '...')) ?></td>
        <?php if($certificate->isPending()) { ?>
            <td class="pending"><em>(pending)</em></td>
        <?php } else { ?>
            <td class="<?php echo $certificate->isExpiringOrInvalid() ? 'err' : 'ok' ?>"><abbr title="<?php echo $certificate->getExpirationDate()->format('Y-m-d H:i:s') ?>"><?php echo $certificate->getExpirationInterval() ?></abbr></td>
        <?php } ?>
        <td class="tools">

            <a href="detail?domain=<?php e($certificate->getName()) ?>" class="btn-show">Detail</a>
            <a href="_delete?domain=<?php e($certificate->getName()) ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>

<?php
include("_footer.php");