<?php
/*
 * Copyright (C) 2016 E-ComProcessing
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
 * @copyright   2016 E-ComProcessing
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

/**
 * Class EComProcessing_Genesis_Model_Admin_Checkout_Options_Transaction_Type
 *
 * Admin options Drop-down for Genesis Checkout Transaction Types
 */
class EComProcessing_Genesis_Model_Admin_Checkout_Options_Transaction_Type
{
    /**
     * Pre-load the required files
     */
    public function __construct()
    {
        /** @var EComProcessing_Genesis_Helper_Data $helper */
        $helper = Mage::helper('ecomprocessing');

        $helper->initLibrary();
    }
    /**
     * Return the transaction types for an Options field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach (static::getTransactionTypes() as $code => $name) {
            $options[] = array(
                'value' => $code,
                'label' => $name
            );
        }

        return $options;
    }

    /**
     * Get the transaction types as:
     *
     * key   = Code Name
     * value = Localized Name
     *
     * @return array
     */
    static function getTransactionTypes()
    {
        return array(
            \Genesis\API\Constants\Transaction\Types::ABNIDEAL =>
                Mage::helper('ecomprocessing')->__('ABN iDEAL'),
            \Genesis\API\Constants\Transaction\Types::AUTHORIZE =>
                Mage::helper('ecomprocessing')->__('Authorize'),
            \Genesis\API\Constants\Transaction\Types::AUTHORIZE_3D =>
                Mage::helper('ecomprocessing')->__('Authorize (3D-Secure)'),
            \Genesis\API\Constants\Transaction\Types::CASHU =>
                Mage::helper('ecomprocessing')->__('CashU'),
            \Genesis\API\Constants\Payment\Methods::EPS =>
                Mage::helper('ecomprocessing')->__('eps'),
            \Genesis\API\Constants\Payment\Methods::GIRO_PAY =>
                Mage::helper('ecomprocessing')->__('GiroPay'),
            \Genesis\API\Constants\Transaction\Types::NETELLER =>
                Mage::helper('ecomprocessing')->__('Neteller'),
            \Genesis\API\Constants\Payment\Methods::QIWI =>
                Mage::helper('ecomprocessing')->__('Qiwi'),
            \Genesis\API\Constants\Transaction\Types::PAYSAFECARD =>
                Mage::helper('ecomprocessing')->__('PaySafeCard'),
            \Genesis\API\Constants\Transaction\Types::PAYBYVOUCHER_SALE =>
                Mage::helper('ecomprocessing')->__('PayByVoucher (Sale)'),
            \Genesis\API\Constants\Transaction\Types::PAYBYVOUCHER_YEEPAY =>
                Mage::helper('ecomprocessing')->__('PayByVoucher (oBeP)'),
            \Genesis\API\Constants\Payment\Methods::PRZELEWY24 =>
                Mage::helper('ecomprocessing')->__('Przelewy24'),
            \Genesis\API\Constants\Transaction\Types::POLI =>
                Mage::helper('ecomprocessing')->__('POLi'),
            \Genesis\API\Constants\Payment\Methods::SAFETY_PAY =>
                Mage::helper('ecomprocessing')->__('SafetyPay'),
            \Genesis\API\Constants\Transaction\Types::SALE =>
                Mage::helper('ecomprocessing')->__('Sale'),
            \Genesis\API\Constants\Transaction\Types::SALE_3D =>
                Mage::helper('ecomprocessing')->__('Sale (3D-Secure)'),
            \Genesis\API\Constants\Transaction\Types::SOFORT =>
                Mage::helper('ecomprocessing')->__('SOFORT'),
            \Genesis\API\Constants\Payment\Methods::TELEINGRESO =>
                Mage::helper('ecomprocessing')->__('TeleIngreso'),
            \Genesis\API\Constants\Payment\Methods::TRUST_PAY =>
                Mage::helper('ecomprocessing')->__('TrustPay'),
            \Genesis\API\Constants\Transaction\Types::WEBMONEY =>
                Mage::helper('ecomprocessing')->__('WebMoney'),
        );
    }
}