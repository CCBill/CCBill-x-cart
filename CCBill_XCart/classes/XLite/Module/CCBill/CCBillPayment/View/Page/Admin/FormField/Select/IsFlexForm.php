<?php

/**
 * Copyright (c) 2019-2021 CCBill
 */

namespace XLite\Module\CCBill\CCBillPayment\View\Page\Admin\FormField\Select;


class IsFlexForm extends \XLite\View\FormField\Select\Regular
{
    /**
     * Yes/No mode values
     */
    const YES = 'Y';
    const NO  = 'N';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::YES => static::t('Yes1'),
            static::NO  => static::t('No1'),
        );
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === true || $value === '1' || $value === 1) {
            $value = static::YES;

        } elseif ($value === false || $value === '0' || $value === 0) {
            $value = static::NO;
        }

        return $value;
    }
}
