<?php

use app\models\ext\Invoice;
use yii\web\View;

/** @var View $this */
/** @var Invoice[] $invoices */

?>

<div class="w-100 min-vh-100 bg-white">
    <?php foreach ($invoices as $invoice) : ?>
        <?php
        echo $this->render('view', [
            'invoice' => $invoice
        ]);
        ?>

        <span style="page-break-after: always;"></span>
    <?php endforeach; ?>
</div>

<script type="application/javascript">
    this.window.print()
</script>