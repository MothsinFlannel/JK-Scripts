<?php /** @noinspection PhpUnhandledExceptionInspection */

use app\models\ext\Invoice;
use vr\core\ArrayHelper;

/** @var Invoice $invoice */

?>

<div id="__nuxt">
    <!---->
    <div id="__layout">
        <div class="w-100 min-vh-100 bg-white">
            <!---->
            <div class="container min-vh-100 d-flex flex-column py-4" style="page-break-before: always;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-uppercase font-weight-bold" style="font-size: 2rem;">
                        vCard
                    </div>

                    <div>app.vcardsys.com</div>
                </div>
                <div style="margin-top: 1rem; margin-bottom: 3rem">
                    <div>
                        <h1 style="color: rgb(113, 116, 141);">
                            Invoice #
                            <?= $invoice->id ?>
                        </h1>
                    </div>
                    <div class="border-top border-bottom py-4">

                        <table role="table" class="" style="width: 100%;">
                            <tbody role="rowgroup">
                            <tr role="row" class="">
                                <td role="cell" class="" style="width: 50%">Period Start:
                                    <?= Yii::$app->formatter->asDate($invoice->since) ?>
                                </td>
                                <td role="cell" class="" style="width: 50%"><b>
                                        <?= $invoice->location->name ?>
                                    </b></td>
                            </tr>

                            <tr role="row" class="">
                                <td role="cell" class="" style="width: 50%">Period End:
                                    <?= Yii::$app->formatter->asDate($invoice->until) ?>
                                </td>
                                <td role="cell" class="" style="width: 50%">
                                    <?= $invoice->location->address ?>
                                </td>
                            </tr>

                            <tr role="row" class="">
                                <td role="cell" class="" style="width: 50%">Balance Due: <span
                                            class="font-weight-bold">
                                            <?= Yii::$app->formatter->asCurrency($invoice->unpaidAmount, 'usd') ?>
                                        </span></td>
                                <td role="cell" class="" style="width: 50%">
                                    <?= $invoice->location->city ?>,
                                    <span class="text-uppercase">
                                            <?= $invoice->location->state ?>
                                        </span>,
                                    <?= $invoice->location->zipCode ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive">
                    <table role="table" class="table b-table">
                        <tbody role="rowgroup">
                        <tr role="row" class=""
                            style="border-bottom: 2px solid rgb(222, 226, 230);text-transform:uppercase;">
                            <th role="rowheader" scope="row" class="">#</th>
                            <th role="rowheader" scope="row" class="">Terminal</th>
                            <th role="rowheader" scope="row" class="">Total In</th>
                            <th role="rowheader" scope="row" class="">Total Out</th>
                            <th role="rowheader" scope="row" class="">Revenue</th>
                            <th role="rowheader" scope="row" class="">Notes</th>
                        </tr>
                        <!---->

                        <?php foreach ($invoice->getInvoiceItems()->each() as $item): ?>
                            <tr role="row" class="">
                                <td role="cell" class="">
                                    <?= $item->number ?>
                                </td>
                                <td role="cell" class="">
                                    <?= $item->title ?>
                                </td>
                                <td role="cell" class="">
                                    <?= Yii::$app->formatter->asCurrency($item->totalIn ?? 0, 'usd') ?>
                                </td>
                                <td role="cell" class="">
                                    <?= Yii::$app->formatter->asCurrency($item->totalOut ?? 0, 'usd') ?>
                                </td>
                                <td role="cell" class="">
                                    <?= Yii::$app->formatter->asCurrency($item->revenue ?? 0, 'usd') ?>
                                </td>
                                <td role="cell" class="">
                                    <?= $item->notes ?>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        <tr role="row" class="">
                            <td role="cell" class="">

                            </td>
                            <td role="cell" class="">

                            </td>
                            <td role="cell" class="">
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'totalIn'), 'usd') ?>
                            </td>
                            <td role="cell" class="">
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'totalOut'), 'usd') ?>
                            </td>
                            <td role="cell" class="">
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'revenue'), 'usd') ?>
                            </td>
                            <td role="cell" class="">

                            </td>
                        </tr>

                        <tr role="row" class="">
                            <td colspan="4" role="cell">
                                Notes:
                                <?= $invoice->notes ?>
                            </td>
                            <td colspan="2" role="cell" class="text-right">
                                Revenue to split:
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'revenue'), 'usd') ?>
                                <br/>
                                Due to Location:
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'revenue') - ArrayHelper::getValue($invoice->totals, 'balance'), 'usd') ?>
                                <br/>
                                Due to Company:
                                <?= Yii::$app->formatter->asCurrency(ArrayHelper::getValue($invoice->totals, 'balance'), 'usd') ?>
                                <br/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-auto text-center" style="padding-top: 6rem;">
                    Payment is due upon receipt. Thank you for your business!
                </div>
            </div>
        </div>
    </div>
</div>