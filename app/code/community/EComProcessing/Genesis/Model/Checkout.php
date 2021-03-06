<?php
/*
 * Copyright (C) 2015 E-ComProcessing™
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      E-ComProcessing
 * @copyright   2015 E-ComProcessing™
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

class EComProcessing_Genesis_Model_Checkout extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'ecomprocessing_checkout';

    protected $_formBlockType = 'ecomprocessing/form_checkout';
    protected $_infoBlockType = 'ecomprocessing/info_checkout';

    protected $_isGateway         = true;
    protected $_canOrder          = true;
    protected $_canAuthorize      = true;
    protected $_canCapture        = true;
    protected $_canCapturePartial = true;
    protected $_canRefund         = true;
    protected $_canVoid           = true;
    protected $_canUseInternal    = false;
    protected $_canUseCheckout    = true;

    protected $_canFetchTransactionInfo = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;

    /**
     * WPF Create method piggyback-ing the Magento's internal Authorize method
     *
     * @param Mage_Sales_Model_Order_Payment|Varien_Object $payment
     * @param String $amount
     * @return EComProcessing_Genesis_Model_Checkout
     * @throws Mage_Core_Exception
     */
    public function order(Varien_Object $payment, $amount)
    {
        Mage::log('Checkout transaction for order #' . $payment->getOrder()->getIncrementId());

        try {
            $this->getHelper()->initClient($this->getCode());

            /** @var Mage_Sales_Model_Order $order */
            $order = $payment->getOrder();

            $billing  = $order->getBillingAddress();
            $shipping = $order->getShippingAddress();

            $genesis = new \Genesis\Genesis('WPF\Create');

            $genesis
                ->request()
                    ->setTransactionId(
                        $this->getHelper()->genTransactionId(
                            $order->getIncrementId()
                        )
                    )
                    ->setCurrency($order->getBaseCurrencyCode())
                    ->setAmount($amount)
                    ->setUsage(
                        $this->getHelper()->__('Magento Payment')
                    )
                    ->setDescription($this->getHelper()->getItemList($order))
                    ->setCustomerPhone($billing->getTelephone())
                    ->setCustomerEmail($order->getCustomerEmail())
                    ->setNotificationUrl(
                        $this->getHelper()->getNotifyURL('checkout')
                    )
                    ->setReturnSuccessUrl(
                        $this->getHelper()->getSuccessURL('checkout')
                    )
                    ->setReturnFailureUrl(
                        $this->getHelper()->getFailureURL('checkout')
                    )
                    ->setReturnCancelUrl(
                        $this->getHelper()->getCancelURL('checkout')
                    )
                    ->setBillingFirstName($billing->getData('firstname'))
                    ->setBillingLastName($billing->getData('lastname'))
                    ->setBillingAddress1($billing->getStreet(1))
                    ->setBillingAddress2($billing->getStreet(2))
                    ->setBillingZipCode($billing->getPostcode())
                    ->setBillingCity($billing->getCity())
                    ->setBillingState($billing->getRegion())
                    ->setBillingCountry($billing->getCountry())
                    ->setShippingFirstName($shipping->getData('firstname'))
                    ->setShippingLastName($shipping->getData('lastname'))
                    ->setShippingAddress1($shipping->getStreet(1))
                    ->setShippingAddress2($shipping->getStreet(2))
                    ->setShippingZipCode($shipping->getPostcode())
                    ->setShippingCity($shipping->getCity())
                    ->setShippingState($shipping->getRegion())
                    ->setShippinCountry($shipping->getCountry())
                    ->setLanguage($this->getHelper()->getLocale());

            foreach ($this->getTransactionTypes() as $transaction_type) {
                if (is_array($transaction_type)) {
                    $genesis->request()->addTransactionType(
                        $transaction_type['name'], $transaction_type['parameters']
                    );
                } else {
                    $genesis->request()->addTransactionType($transaction_type);
                }
            }

            $genesis->execute();

            $payment
                ->setTransactionId(
                    $genesis->response()->getResponseObject()->unique_id
                )
                ->setIsTransactionPending(true)
                ->addTransaction(
                    Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
                );


            $payment->setSkipTransactionCreation(true);

            // Save the redirect url with our
            $this->getHelper()->getCheckoutSession()->setEComProcessingCheckoutRedirectUrl(
                $genesis->response()->getResponseObject()->redirect_url
            );
        } catch (Exception $exception) {
            Mage::logException($exception);

            Mage::throwException(
                $this->getHelper()->__($exception->getMessage())
            );
        }

        return $this;
    }

    /**
     * @param Varien_Object|Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return $this|bool
     * @throws Mage_Core_Exception
     */
    public function capture(Varien_Object $payment, $amount)
    {
        Mage::log('Capture transaction for order #' . $payment->getOrder()->getIncrementId());

        try {
            $this->getHelper()->initClient($this->getCode());

            $this->getHelper()->setTokenByPaymentTransaction($payment);

            $authorize = $payment->lookupTransaction(null, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);

            /* Capture should only be possible, when Authorize Transaction Exists */
            if (!isset($authorize) || $authorize === false) {
                Mage::log('Capture transaction for order #' . $payment->getOrder()->getIncrementId() . ' cannot be finished (No Authorize Transaction exists)');
                return $this; 
            }
            
            $reference_id = $authorize->getTxnId();

            $genesis = new \Genesis\Genesis('Financial\Capture');

            $genesis
                ->request()
                    ->setTransactionId(
                        $this->getHelper()->genTransactionId(
                            $payment->getOrder()->getIncrementId()
                        )
                    )
                    ->setRemoteIp(
                        $this->getHelper('core/http')->getRemoteAddr(false)
                    )
                    ->setReferenceId(
                        $reference_id
                    )
                    ->setCurrency(
                        $payment->getOrder()->getBaseCurrencyCode()
                    )
                    ->setAmount(
                        $amount
                    );

            $genesis->execute();

            $payment
                ->setTransactionId(
                    $genesis->response()->getResponseObject()->unique_id
                )
                ->setParentTransactionId(
                    $reference_id
                )
                ->setShouldCloseParentTransaction(
                    true
                )
                ->resetTransactionAdditionalInfo(

                )
                ->setTransactionAdditionalInfo(
                    array(
                        Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->getArrayFromGatewayResponse(
                            $genesis->response()->getResponseObject()
                        )
                    ),
                    null
                );

            $payment->save();
        } catch (Exception $exception) {
            Mage::logException($exception);

            Mage::throwException(
                $this->getHelper()->__($exception->getMessage())
            );
        }

        return $this;
    }

    /**
     * Refund the last successful transaction
     *
     * @param Varien_Object|Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     *
     * @return EComProcessing_Genesis_Model_Checkout
     */
    public function refund(Varien_Object $payment, $amount)
    {
        Mage::log('Refund transaction for order #' . $payment->getOrder()->getIncrementId());

        try {
            $this->getHelper()->initClient($this->getCode());

            $this->getHelper()->setTokenByPaymentTransaction($payment);

            $capture = $payment->lookupTransaction(null, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);

            /* Refund Transaction is only possible, when Capture Transaction Exists */
            if (!isset($capture) || $capture === false) {
              Mage::log('Refund transaction for order #' . $payment->getOrder()->getIncrementId() . ' could not be completed! (No Capture Transaction Exists');
              return $this; 
            }
            
            $reference_id = $capture->getTxnId();

            $genesis = new \Genesis\Genesis('Financial\Refund');

            $genesis
              ->request()
                  ->setTransactionId(
                      $this->getHelper()->genTransactionId(
                          $payment->getOrder()->getIncrementId()
                      )
                  )
                  ->setRemoteIp(
                      $this->getHelper('core/http')->getRemoteAddr(false)
                  )
                  ->setReferenceId(
                      $reference_id
                  )
                  ->setCurrency(
                      $payment->getOrder()->getBaseCurrencyCode()
                  )
                  ->setAmount($amount);

            $genesis->execute();

            $payment
                ->setTransactionId(
                    $genesis->response()->getResponseObject()->unique_id
                )
                ->setParentTransactionId(
                    $reference_id
                )
                ->setShouldCloseParentTransaction(
                    true
                )
                ->resetTransactionAdditionalInfo(

                )
                ->setTransactionAdditionalInfo(
                    array(
                        Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->getArrayFromGatewayResponse(
                            $genesis->response()->getResponseObject()
                        )
                    ),
                    null
                );

            $payment->save();
        } catch (Exception $exception) {
            Mage::logException($exception);

            Mage::throwException(
                $exception->getMessage()
            );
        }

        return $this;
    }

    /**
     * Void the last successful transaction
     *
     * @param Varien_Object|Mage_Sales_Model_Order_Payment $payment
     *
     * @return EComProcessing_Genesis_Model_Checkout
     */
    public function void(Varien_Object $payment)
    {
        Mage::log('Void transaction for order #' . $payment->getOrder()->getIncrementId());

        try {
            $this->getHelper()->initClient($this->getCode());

            $this->getHelper()->setTokenByPaymentTransaction($payment);

            $transactions = $this->getHelper()->getTransactionFromPaymentObject($payment, array(
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH,
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE
            ));

            $reference_id = $transactions ? reset($transactions)->getTxnId() : null;

            $genesis = new \Genesis\Genesis('Financial\Void');

            $genesis
                ->request()
                    ->setTransactionId(
                        $this->getHelper()->genTransactionId(
                            $payment->getOrder()->getIncrementId()
                        )
                    )
                    ->setRemoteIp(
                        $this->getHelper('core/http')->getRemoteAddr(false)
                    )
                    ->setReferenceId(
                        $reference_id
                    );

            $genesis->execute();

            $payment
                ->setTransactionId(
                    $genesis->response()->getResponseObject()->unique_id
                )
                ->setParentTransactionId(
                    $reference_id
                )
                ->setShouldCloseParentTransaction(
                    true
                )
                ->resetTransactionAdditionalInfo(

                )
                ->setTransactionAdditionalInfo(
                    array(
                        Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->getArrayFromGatewayResponse(
                            $genesis->response()->getResponseObject()
                        )
                    ),
                    null
                );

            $payment->save();
        } catch (Exception $exception) {
            Mage::logException($exception);

            Mage::throwException(
                $exception->getMessage()
            );
        }

        return $this;
    }

    /**
     * Cancel payment abstract method
     *
     * @param Varien_Object $payment
     *
     * @return EComProcessing_Genesis_Model_Checkout
     */
    public function cancel(Varien_Object $payment)
    {
        return $this->void($payment);
    }

    /**
     * Fetch transaction details info
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        /** @var Mage_Sales_Model_Order_Payment_Transaction $transaction */
        $transaction = Mage::getModel('sales/order_payment_transaction')->load($transactionId, 'txn_id');

        $checkout_transaction = $transaction->getOrder()->getPayment()->lookupTransaction(
            null,
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER
        );

        $reconcile = $this->reconcile($checkout_transaction->getTxnId());

        // Get the current details
        $transaction_details = $payment->getAdditionalInformation(
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
        );

        // Try to extract transaction details from the Gateway response
        if ($reconcile->unique_id == $transactionId) {
            $transaction_details = $reconcile;
        }
        else {
            if ($reconcile->payment_transaction instanceof stdClass) {
                if ($reconcile->payment_transaction->unique_id == $transactionId) {
                    $transaction_details = $reconcile->payment_transaction;
                }
            }

            if ($reconcile->payment_transaction instanceof ArrayObject) {
                foreach ($reconcile->payment_transaction as $payment_transaction) {
                    if ($payment_transaction->unique_id == $transactionId) {
                        $transaction_details = $payment_transaction;
                    }
                }
            }
        }

        // Remove the current details
        $payment->unsAdditionalInformation(
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
        );

        // Set the default/updated transaction details
        $payment->setAdditionalInformation(
            array(
                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->getArrayFromGatewayResponse(
                    $transaction_details
                )
            ),
            null
        );

        $payment->save();

        return $payment->getAdditionalInformation(
            Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
        );
    }

    /**
     * Execute a WPF Reconcile
     *
     * @param $unique_id
     *
     * @return EComProcessing_Genesis_Model_Checkout
     *
     * @throws Mage_Core_Exception
     */
    public function reconcile($unique_id)
    {
        try {
            $this->getHelper()->initClient($this->getCode());

            $genesis = new \Genesis\Genesis('WPF\Reconcile');

            $genesis->request()->setUniqueId($unique_id);

            $genesis->execute();

            return $genesis->response()->getResponseObject();
        } catch (Exception $exception) {
            Mage::logException($exception);

            Mage::throwException(
                $exception->getMessage()
            );
        }

        return false;
    }

    public function processNotification($checkout_transaction)
    {
        try {
            $this->getHelper()->initClient($this->getCode());

            /** @var Mage_Sales_Model_Order_Payment_Transaction $transaction */
            $transaction = Mage::getModel('sales/order_payment_transaction')->load($checkout_transaction->unique_id, 'txn_id');

            $order = $transaction->getOrder();

            if ($order) {
                $transaction
                    ->setOrderPaymentObject(
                        $order->getPayment()
                    )
                    ->setAdditionalInformation(
                        Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,
                        $this->getHelper()->getArrayFromGatewayResponse(
                            $checkout_transaction
                        )
                    )
                    ->save();

                if (isset($checkout_transaction->payment_transaction)) {
                    $payment_transaction = $checkout_transaction->payment_transaction;

                    $payment = $order->getPayment();

                    $payment
                        ->setTransactionId($payment_transaction->unique_id)
                        ->setParentTransactionId($checkout_transaction->unique_id)
                        ->setShouldCloseParentTransaction(true)
                        ->setIsTransactionPending(false)
                        ->resetTransactionAdditionalInfo()
                        ->setTransactionAdditionalInfo(
                            array(
                                Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS => $this->getHelper()->getArrayFromGatewayResponse(
                                    $payment_transaction
                                )
                            ),
                            null
                        );

                    if ($payment_transaction->status == \Genesis\API\Constants\Transaction\States::APPROVED) {
                        $payment->setIsTransactionClosed(false);
                    }
                    else {
                        $payment->setIsTransactionClosed(true);
                    }

                    switch ($payment_transaction->transaction_type) {
                        case \Genesis\API\Constants\Transaction\Types::AUTHORIZE:
                        case \Genesis\API\Constants\Transaction\Types::AUTHORIZE_3D:
                            $payment->registerAuthorizationNotification($payment_transaction->amount, true);
                            break;
                        case \Genesis\API\Constants\Transaction\Types::ABNIDEAL:
                        case \Genesis\API\Constants\Transaction\Types::CASHU:
                        case \Genesis\API\Constants\Transaction\Types::NETELLER:
                        case \Genesis\API\Constants\Transaction\Types::PAYSAFECARD:
                        case \Genesis\API\Constants\Transaction\Types::PPRO:
                        case \Genesis\API\Constants\Transaction\Types::SALE:
                        case \Genesis\API\Constants\Transaction\Types::SALE_3D:
                        case \Genesis\API\Constants\Transaction\Types::SOFORT:
                            $payment->registerCaptureNotification($payment_transaction->amount, true);
                            break;
                        default:
                            break;
                    }


                    $payment->save();
                }

                $this->getHelper()->setOrderState(
                    $order,
                    isset($payment_transaction) ? $payment_transaction->status : $checkout_transaction->status
                );

                return true;
            }
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        return false;
    }

    /**
     * Get the selected transaction types in array
     *
     * @return array
     */
    public function getTransactionTypes()
    {
        $processed_list = array();

        $selected_types = array_filter(
            explode(',', $this->getConfigData('genesis_types'))
        );

        $alias_map = array(
            \Genesis\API\Constants\Payment\Methods::EPS         =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::GIRO_PAY    =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::PRZELEWY24  =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::QIWI        =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::SAFETY_PAY  =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::TELEINGRESO =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
            \Genesis\API\Constants\Payment\Methods::TRUST_PAY   =>
                \Genesis\API\Constants\Transaction\Types::PPRO,
        );

        foreach ($selected_types as $selected_type) {
            if (array_key_exists($selected_type, $alias_map)) {
                $transaction_type = $alias_map[$selected_type];

                $processed_list[$transaction_type]['name'] = $transaction_type;

                $processed_list[$transaction_type]['parameters'][] = array(
                    'payment_method' => $selected_type
                );
            } else {
                $processed_list[] = $selected_type;
            }
        }

        return $processed_list;
    }

    /**
     * Get URL to "Redirect" block
     *
     * @see EComProcessing_Genesis_CheckoutController
     *
     * @note In order for redirect to work, you must
     * set the session variable:
     *
     * EComProcessingCheckoutRedirectUrl
     *
     * @return mixed
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getHelper()->getRedirectUrl('checkout');
    }

    /**
     * Get the helper or return its instance
     *
     * @param $helper string - Name of the helper, empty for the default class helper
     *
     * @return EComProcessing_Genesis_Helper_Data|mixed
     */
    private function getHelper($helper = '')
    {
        if (empty($helper)) {
            return Mage::helper('ecomprocessing');
        } else {
            return Mage::helper($helper);
        }
    }
}