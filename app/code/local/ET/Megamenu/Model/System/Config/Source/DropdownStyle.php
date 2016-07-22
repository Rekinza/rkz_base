<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_DropdownStyle extends Varien_Object {
    
	public static function getOptionArray() {
		return array (
			'default' => Mage::helper('megamenu')->__('Default'),
			'columns' => Mage::helper('megamenu')->__('Columns'),
		    'fullwidth' => Mage::helper('megamenu')->__('Full Width'),
		);
	}
	
	public static function toOptionArray() {
		return array (
			array (
				'value' => 'default',
				'label' => Mage::helper('megamenu')->__('Default')
			),
			array (
				'value' => 'columns',
				'label' => Mage::helper('megamenu')->__('Columns')
			),
		    array (
		        'value' => 'fullwidth',
		        'label' => Mage::helper('megamenu')->__('Full Width')
		    )
		);
	}
	
}