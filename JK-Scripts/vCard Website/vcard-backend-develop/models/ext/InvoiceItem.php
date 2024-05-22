<?php


namespace app\models\ext;


/**
 * Class InvoiceItem
 * @package app\models\ext
 */
class InvoiceItem extends \app\models\InvoiceItem
{
    /**
     *
     */
    const TYPE_EXTRA = 'extra';

    /**
     *
     */
    const TYPE_AUTOMATIC = 'automatic';

    /**
     *
     */
    const TYPE_MANUAL = 'manual';
}