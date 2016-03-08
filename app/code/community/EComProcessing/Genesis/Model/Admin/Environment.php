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
 * Class EComProcessing_Genesis_Model_Admin_Environment
 *
 * Admin options Drop-down for Gateway environment
 */
class EComProcessing_Genesis_Model_Admin_Environment
{
    /**
     * Return the environment types for an Options field
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();

        foreach (static::getEnvironmentOptions() as $code => $name) {
            $options[] = array(
                'value' => $code,
                'label' => $name
            );
        }

        return $options;
    }

    /**
     * Get the available environment types
     *
     * @return array
     */
    static function getEnvironmentOptions()
    {
        return array(
            'sandbox'       => Mage::helper('ecomprocessing')->__('Yes'),
            'production'    => Mage::helper('ecomprocessing')->__('No'),
        );
    }
}