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
 * Class EComProcessing_Genesis_Model_Admin_Direct_Options_Transaction_Type
 *
 * Admin options Drop-down for Genesis Direct Transaction Types
 */
class EComProcessing_Genesis_Model_Admin_Direct_Options_Transaction_Type
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
            \Genesis\API\Constants\Transaction\Types::AUTHORIZE =>
                Mage::helper('ecomprocessing')->__('Authorize'),
            \Genesis\API\Constants\Transaction\Types::AUTHORIZE_3D =>
                Mage::helper('ecomprocessing')->__('Authorize (3D-Secure)'),
            \Genesis\API\Constants\Transaction\Types::SALE =>
                Mage::helper('ecomprocessing')->__('Sale'),
            \Genesis\API\Constants\Transaction\Types::SALE_3D =>
                Mage::helper('ecomprocessing')->__('Sale (3D-Secure)'),
        );
    }
}