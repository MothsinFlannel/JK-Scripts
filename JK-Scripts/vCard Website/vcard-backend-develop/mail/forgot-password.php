<?php

/** @var app\models\ext\User $user */
/** @var string $url */
?>

<p>
    Dear <?= $user->fullName ?>,
</p>

<p>
    Somebody perhaps you requested password recovery. If it was you please follow this link to recover your password. Otherwise, just ignore it
</p>

<a href="<?= $url ?>"><?= $url ?></a>

