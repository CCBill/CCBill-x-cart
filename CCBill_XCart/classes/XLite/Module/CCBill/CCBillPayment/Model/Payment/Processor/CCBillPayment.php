<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2014-2021 CCBill <support@ccbill.com>. All rights reserved
 * See https://www.ccbill.com/license-agreement.html for license details.
 */

namespace XLite\Module\CCBill\CCBillPayment\Model\Payment\Processor;

/**
 * CCBill processor
 *
 */
class CCBillPayment extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return array(
            self::OPERATION_SALE,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/CCBill/CCBillPayment/config.twig';
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
    /*
          static::log(
              array('line' => 54, 'method' => 'processReturn', 'request' => \XLite\Core\Request::getInstance()->getData())
          );

          static::log(
              array('line' => 58, 'method' => 'processReturn', 'request' => $_POST)
          );

          static::log(
              array('line' => 62, 'method' => 'processReturn', 'request' => $_REQUEST)
          );
                  */
          parent::processReturn($transaction);

          $request = \XLite\Core\Request::getInstance();


        //$status = $request->cart_order_id ? $transaction::STATUS_SUCCESS : $transaction::STATUS_FAILED;

        if($request->action && $request->cart_order_id){

          $success = 0;

          $responseDigest = $_POST['responseDigest'];

          $txid = $_POST['subscription_id'];

          if ($request->action == 'Approval_Post' && $request->subscription_id && $txId == $request->subscription_id) {

            $subscriptionIdToHash = $txId;

            if($currentMethod->is_flexform == 'yes') {
              $subscriptionIdToHash = ltrim($txId, '0');
            }// end if

            // Validate response digest
            $stringToHash = $subscriptionIdToHash . '1' . $this->getSetting('salt');

            $myDigest = md5($stringToHash);

            if($myDigest == $responseDigest)
              $success = 1;

          }// end if

          if($success === 1){

              $this->setDetail('authCode', $request->subscription_id, 'Auth code');

              //$this->transaction->setNote('CCBill Payment Completed.  Subscription ID: ' . $request->subscription_id);

              $status = $transaction::STATUS_SUCCESS;

              $this->transaction->setStatus($status);
              $transaction->getOrder()->setPaymentStatusByTransaction($transaction);

              // And again, for some reason the first time only sets to "Authorized"
              // and we need to do another lap to get to "paid"
              $status = $transaction::STATUS_SUCCESS;

              $this->transaction->setStatus($status);
              $transaction->getOrder()->setPaymentStatusByTransaction($transaction);


          }
          else if($request->action == 'Denial_Post' && $request->reasonForDecline){
              $this->setDetail('verification', 'CCBill Payment Failed.  ' . $request->reasonForDecline, 'Verification');


              //$this->transaction->setNote('CCBill Payment Failed');
              $status = $transaction::STATUS_FAILED;
              $this->transaction->setStatus($status);
          }

        }// end if action is defined
    }



    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */

    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        static::log(
            array('line' => 125, 'method' => 'processCallback', 'request' => 'This is a test')
        );

        static::log(
            array('line' => 129, 'method' => 'processCallback', 'request' => \XLite\Core\Request::getInstance()->getData())
        );

    }// end processCallback


    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('client_account_no')
            && $method->getSetting('client_subaccount_no')
            && $method->getSetting('form_name')
            && $method->getSetting('is_flexform')
            && $method->getSetting('currency')
            && $method->getSetting('salt');
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function Type()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'client_account_no',
            'client_subaccount_no',
            'form_name',
            'is_flexform',
            'currency',
            'salt'
        );
    }

    /**
     * Get return request owner transaction or null
     *
     * @return \XLite\Model\Payment\Transaction|void
     */
    public function getReturnOwnerTransaction()
    {
        return \XLite\Core\Request::getInstance()->cart_order_id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneByPublicTxnId(\XLite\Core\Request::getInstance()->cart_order_id)
            : null;
    }

    public function getCCBillCurrencyCode($currency){
      $rVal = 840;
      switch($currency){
        case "USD": $rVal = 840;
          break;
        case "EUR": $rVal = 978;
          break;
        case "AUD": $rVal = 036;
          break;
        case "CAD": $rVal = 124;
          break;
        case "GBP": $rVal = 826;
          break;
        case "JPY": $rVal = 392;
          break;

      }
      return $rVal;
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }


    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        if($this->getSetting('is_flexform') == 'true')
            return 'https://api.ccbill.com/wap-frontflex/flexforms/' . $this->getSetting('form_name');
        else
            return 'https://bill.ccbill.com/jpost/signup.cgi';
    }

    /**
     * Format name for request. (firstname + lastname from shipping/billing address)
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getName($address)
    {
        return $address->getFirstname()
            . ' ' . $address->getLastname();
    }

    /**
     * Format state of billing address for request
     *
     * @return string
     */
    protected function getBillingState()
    {
        return $this->getState($this->getProfile()->getBillingAddress());
    }

    /**
     * Format state of shipping address for request
     *
     * @return string
     */
    protected function getShippingState()
    {
        return $this->getState($this->getProfile()->getShippingAddress());
    }

    /**
     * Format state that is provided from $address model for request.
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getState($address)
    {
        $state = $this->getStateFieldValue($address);

        if (empty($state)) {
            $state = 'n/a';
        } elseif (!in_array($this->getCountryField($address), array('US', 'CA'))) {
            $state = 'XX';
        }

        return $state;
    }

    /**
     * Return State field value. If country is US then state code must be used.
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getStateFieldValue($address)
    {
        return 'US' === $this->getCountryField($address)
            ? $address->getState()->getCode()
            : $address->getState()->getState();
    }

    /**
     * Return Country field value. if no country defined we should use '' value
     *
     * @param \XLite\Model\Address $address Address model (could be shipping or billing address)
     *
     * @return string
     */
    protected function getCountryField($address)
    {
        return $address->getCountry()
            ? $address->getCountry()->getCode()
            : '';
    }

    /**
     * Return formatted price.
     *
     * @param float $price Price value
     *
     * @return string
     */
    protected function getFormattedPrice($price)
    {
        return sprintf('%.2f', round((double)($price) + 0.00000000001, 2));
    }


    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $isFlexForm = $this->getSetting('is_flexform') == 'true';

    		$priceVarName      = 'formPrice';
    		$periodVarName     = 'formPeriod';

    		if($isFlexForm){
    		  $priceVarName      = 'initialPrice';
    		  $periodVarName     = 'initialPeriod';
    		}// end if

        $billingPeriodInDays = 2;

        $formPrice = number_format($this->transaction->getValue(), 2, '.', '');

        $cartOrderId = $this->transaction->getPublicTxnId();

        $currencyCode = $this->getCCBillCurrencyCode($this->getSetting('currency'));

        $salt = $this->getSetting('salt');

        $stringToHash = '' . $formPrice
  	                       . $billingPeriodInDays
  	                       . $currencyCode
  	                       . $salt;

  	    $myDigest = md5($stringToHash);


  	    $address   = $this->getProfile()->getBillingAddress();



        $fields = array(
            'clientAccnum'        => $this->getSetting('client_account_no'),
            'clientSubacc'        => $this->getSetting('client_subaccount_no'),
            'formName'            => $this->getSetting('form_name'),
            'currencyCode'        => $currencyCode,
            $priceVarName         => $formPrice,
            $periodVarName        => $billingPeriodInDays,
            'formDigest'          => $myDigest,
            //'zc_orderid'          => $cartOrderId,
            //'txnId'               => $cartOrderId,
            'cart_order_id'       => $cartOrderId,
            'merchant_order_id'   => $this->getSetting('prefix') . $this->getOrder()->getOrderNumber(),
            'order_number'        => $this->getSetting('prefix') . $this->getOrder()->getOrderNumber(),

            'customer_fname'      => $address->getFirstname(),
            'customer_lname'      => $address->getLastname(),
            'email'               => $this->getProfile()->getLogin(),
            'zipcode'             => $this->getProfile()->getBillingAddress()->getZipcode(),
            'country'             => $this->getCountryField($address),
            'city'                => $this->getProfile()->getBillingAddress()->getCity(),
            'state'               => $this->getBillingState(),
            'address1'            => $this->getProfile()->getBillingAddress()->getStreet(),

        );

        static::log(
            array('line' => 397, 'method' => 'getFormFields', 'fields' => $fields)
        );

        return $fields;
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            array($method->getSetting('currency'))
        );
    }



    /**
     * Logging the data under CCBill
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data
     *
     * @return void
     */
    protected static function log($data)
    {
        //if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('CCBillPayment', $data);
        //}
    }
}
