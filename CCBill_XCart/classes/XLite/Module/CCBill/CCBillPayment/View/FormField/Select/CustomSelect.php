<?php

namespace XLite\Module\CCBill\CCBillPayment\View\FormField\Select;

class CustomSelect extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        return array(
            'First value'  => static::t('First value'),
            'Second value' => static::t('Second value'),
            'Third value'  => static::t('Third value'),
        );
    }
}
