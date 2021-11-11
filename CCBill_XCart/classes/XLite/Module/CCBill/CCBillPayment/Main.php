<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2014-2021 CCBill <support@ccbill.com>. All rights reserved
 * See https://www.ccbill.com/license-agreement.html for license details.
 */

namespace XLite\Module\CCBill\CCBillPayment;

use XLite\Core\Cache\ExecuteCached;
/**
 * CCBill module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Author name
     *
     * @return string
     */
    public static function getAuthorName()
    {
        return 'CCBill';
    }

    /**
     * Module name
     *
     * @return string
     */
    public static function getModuleName()
    {
        return 'CCBill Payments';
    }

    /**
     * Module description
     *
     * @return string
     */
    public static function getDescription()
    {
        return 'Accept credit card payments via CCBill.';
    }

    /**
     * Get module major version
     *
     * @return string
     */
    public static function getMajorVersion()
    {
        return '5.3';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getMinorVersion()
    {
        return '6';
    }

    /**
     * Module version
     *
     * @return string
     */
    public static function getBuildVersion()
    {
        return '0';
    }

    /**
     * Get minor core version which is required for the module activation
     *
     * @return string
     */
    public static function getMinorRequiredCoreVersion()
    {
        return '5';
    }

    /**
     * The module is defined as the payment module
     *
     * @return integer|null
     */
    public static function getModuleType()
    {
        return static::MODULE_TYPE_PAYMENT;
    }

    public static function log($message)
    {
        \XLite\Logger::logCustom('ccbill_pa', $message);
    }

    /*
    /**
     * Defines the link for the payment settings form
     *
     * @return string
     */

    public static function getPaymentSettingsForm()
    {
        return \XLite\Core\Converter::buildURL(
            'module',
            '',
            [
                'moduleId'     => \XLite\Core\Database::getRepo('XLite\Model\Module')
                    ->findOneBy(['author' => 'CCBill', 'name' => 'CCBillPayment', 'fromMarketplace' => false])
                    ->getModuleId(),
                'returnTarget' => 'addons_list_installed',
            ]
        );
    }

}
