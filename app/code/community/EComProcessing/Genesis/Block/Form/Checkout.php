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
 * Class EComProcessing_Genesis_Block_Form_Checkout
 *
 * Form Block for Checkout method
 */
class EComProcessing_Genesis_Block_Form_Checkout extends Mage_Payment_Block_Form
{
    /**
     * Setup
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ecomprocessing/form/checkout.phtml');
    }
}