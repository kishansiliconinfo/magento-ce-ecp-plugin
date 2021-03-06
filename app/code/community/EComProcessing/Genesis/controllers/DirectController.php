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

/**
 * Class EComProcessing_Genesis_DirectController
 *
 * Front-end method for Direct method
 */
class EComProcessing_Genesis_DirectController extends Mage_Core_Controller_Front_Action
{
    /** @var EComProcessing_Genesis_Helper_Data $helper */
    private $helper;

    /** @var EComProcessing_Genesis_Model_Direct $direct */
    private $direct;

    protected function _construct()
    {
        $this->helper = Mage::helper('ecomprocessing');

        $this->direct = Mage::getModel('ecomprocessing/direct');
    }

    /**
     * Process an incoming Genesis Notification
     * If it appears valid, do a reconcile and
     * use the reconcile data to save details
     * about the transaction
     *
     * @see Genesis_API_Documentation \ notification_url
     *
     * @return void
     */
    public function notifyAction()
    {
        // Notifications are only POST, deny everything else
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            $this->helper->initClient($this->direct->getCode());

            $notification = new \Genesis\API\Notification(
                $this->getRequest()->getPost()
            );

            if ($notification->isAuthentic()) {
                $notification->initReconciliation();

                $reconcile = $notification->getReconciliationObject();

                if (isset($reconcile->unique_id)) {
                    $this->direct->processNotification($reconcile);

                    $this->getResponse()->setHeader('Content-type', 'application/xml');

                    $this->getResponse()->setBody(
                        $notification->generateResponse()
                    );
                }
            }
        } catch (Exception $exception) {
            Mage::logException($exception);
        }
    }

    /**
     * When a customer has to be redirected, show
     * a "transition" page where you notify them,
     * that they will be redirected to a new website.
     *
     * @return void
     */
    public function redirectAction()
    {
        $this->helper->redirectIfNotLoggedIn();

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ecomprocessing/redirect_direct')->toHtml()
        );
    }

    /**
     * Customer landing page for successful payment
     *
     * @see Genesis_API_Documentation \ return_success_url
     *
     * @return void
     */
    public function successAction()
    {
        $this->helper->redirectIfNotLoggedIn();

        $this->_redirect('checkout/onepage/success', array('_secure' => true));
    }

    /**
     * Customer landing page for unsuccessful payment
     *
     * @see Genesis_API_Documentation \ return_failure_url
     *
     * @return void
     */
    public function failureAction()
    {
        $this->helper->redirectIfNotLoggedIn();

        $this->helper->restoreQuote();

        $this->helper->getCheckoutSession()->addError(
            $this->helper->__('We were unable to process your payment! Please check your input or try again later.')
        );

        $this->_redirect('checkout/cart', array('_secure' => true));
    }
}