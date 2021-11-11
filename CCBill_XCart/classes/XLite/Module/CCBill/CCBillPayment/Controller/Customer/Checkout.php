<?php
// vim: set ts=4 sw=4 sts=4 et:

namespace XLite\Module\CCBill\CCBillPayment\Controller\Customer;

/**
 * Checkout controller
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * doPayment
     */
    protected function doPayment()
    {

        $cart = $this->getCart();

        if (isset(\XLite\Core\Request::getInstance()->notes)) {
            $cart->setNotes(\XLite\Core\Request::getInstance()->notes);
        }

        if ($cart instanceOf \XLite\Model\Cart) {
            $cart->setDate(\XLite\Core\Converter::time());

            // Set order number. This ID is incremented only for orders (not for cart entities)
            $cart->assignOrderNumber();
        }

        // Get first (and only) payment transaction
        $transaction = $cart->getFirstOpenPaymentTransaction();
        $result = null;
        $paymentStatusCode = null;

        if ($transaction) {

            // Process transaction
            $result = $transaction->handleCheckoutAction();

            $hasAuthorizedPayment = false;

            foreach ($cart->getPaymentTransactions() as $t) {
                $hasAuthorizedPayment = $hasAuthorizedPayment || $t->isAuthorized();
            }

            if ($hasAuthorizedPayment) {
                $paymentStatusCode = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
            }

        } elseif (!$cart->isOpen()) {

            // Cart is payed - create dump transaction
            $result = \XLite\Model\Payment\Transaction::COMPLETED;
            $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_PAID;
            $hasIncompletePayment = (0 < $cart->getOpenTotal());
            $hasAuthorizedPayment = false;

            foreach ($cart->getPaymentTransactions() as $t) {
                $hasAuthorizedPayment = $hasAuthorizedPayment || $t->isAuthorized();
            }

            if ($hasIncompletePayment) {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;

            } elseif ($hasAuthorizedPayment) {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
                $paymentStatusCode = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
            }

        } else {
            $paymentStatusCode = $cart->getPaymentStatusCode();
        }

        if (\XLite\Model\Payment\Transaction::PROLONGATION == $result) {
            $this->set('silent', true);
/*
            \XLite\Core\TopMessage::addError(
                'You have an unpaid order #{{ORDER}}',
                array(
                    'ORDER' => $cart->getOrderNumber(),
                )
            );
*/
            $cart->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);
            $this->processSucceed();

            exit (0);

        } elseif (\XLite\Model\Payment\Transaction::SILENT == $result) {

            $this->paymentWidgetData = $transaction->getPaymentMethod()
                ->getProcessor()
                ->getPaymentWidgetData();
            $this->set('silent', true);

        } elseif (\XLite\Model\Payment\Transaction::SEPARATE == $result) {

            $cart->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);
            $this->processSucceed();

            $this->setReturnURL($this->buildURL('checkoutPayment'));

        } elseif ($cart->isOpen()) {

            // Order is open - go to Select payment method step
            if ($transaction && $transaction->getNote()) {
                \XLite\Core\TopMessage::getInstance()->add(
                    $transaction->getNote(),
                    array(),
                    null,
                    $transaction->isFailed()
                        ? \XLite\Core\TopMessage::ERROR
                        : \XLite\Core\TopMessage::INFO,
                    true
                );
            }

            $this->setReturnURL($this->buildURL('checkout'));

        } else {

            if ($cart->isPayed()) {
                $paymentStatus = $paymentStatusCode ?: \XLite\Model\Order\Status\Payment::STATUS_PAID;

            } elseif ($transaction && $transaction->isFailed()) {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;

            } else {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
                \XLite\Core\Mailer::getInstance()->sendOrderCreated($cart);
            }

            $this->processSucceed();
            if ($paymentStatus) {
                $cart->setPaymentStatus($paymentStatus);
                \XLite\Core\Database::getEM()->flush();
            }

            \XLite\Core\TopMessage::getInstance()->clearTopMessages();

            $this->setReturnURL(
                $this->buildURL(
                    \XLite\Model\Order\Status\Payment::STATUS_DECLINED == $paymentStatus
                        ? 'checkoutFailed'
                        : 'checkoutSuccess',
                    '',
                    $cart->getOrderNumber()
                        ? array('order_number' => $cart->getOrderNumber())
                        : array('order_id' => $cart->getOrderId())
                )
            );
        }

    }// end doPayment

}
