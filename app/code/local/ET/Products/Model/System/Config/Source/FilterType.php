<?php
/**
 * @package ET_Products
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Products_Model_System_Config_Source_FilterType extends Varien_Object {

	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array (
        	array('value' => 0, 'label' => Mage::helper('products')->__('Default')),
            array('value' => 1, 'label' => Mage::helper('products')->__('Include (only)')),
            array('value' => 2, 'label' => Mage::helper('products')->__('Exclude'))
        );
    }
}