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
 * Class EComProcessing_Genesis_Block_Redirect_Checkout
 *
 * Redirect Block for Checkout method
 */
class EComProcessing_Genesis_Block_Redirect_Checkout extends Mage_Core_Block_Template
{
    /** @var String */
    private $unique_id;
    /** @var EComProcessing_Genesis_Helper_Data $helper */
    private $helper;

    protected function _construct()
    {
        parent::_construct();

        $this->setHelper();

        $this->setUniqueId();

        $this->setTemplate('ecomprocessing/redirect/checkout.phtml');
    }

    /**
     * Generate HTML form
     *
     * @return string
     */
    public function generateForm()
    {
        $form = new Varien_Data_Form();

        $form
            ->setAction(
                $this->helper->getCheckoutSession()->getEComProcessingCheckoutRedirectUrl()
            )
            ->setId('ecomprocessing_redirect_notification')
            ->setName('ecomprocessing_redirect_notification')
            ->setMethod('GET')
            ->setUseContainer(true);

        $submitButton = new Varien_Data_Form_Element_Submit(
            array(
                'value' => $this->__('Click here, if you are not redirected within 10 seconds...'),
            )
        );

        $submitButton->setId(
            $this->getButtonId()
        );

        $form->addElement($submitButton);

        return $form->toHtml();
    }

    /**
     * Get the button id
     *
     * @return string
     */
    public function getButtonId()
    {
        return sprintf('redirect_to_dest_%s', $this->unique_id);
    }

    /**
     * Set Helper
     */
    private function setHelper()
    {
        $this->helper = Mage::helper('ecomprocessing');
    }

    /**
     * Set Unique Id
     */
    private function setUniqueId()
    {
        $this->unique_id = Mage::helper('core')->uniqHash();
    }
}