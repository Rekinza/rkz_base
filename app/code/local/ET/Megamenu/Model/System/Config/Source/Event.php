<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Event extends Varien_Object {
	const HOVER = 0;
	const CLICK = 1;
	public static function getOptionArray() {
		return array (
				self::HOVER => Mage::helper('megamenu')->__( 'Hover' ),
				self::CLICK => Mage::helper('megamenu')->__( 'Click' )
		);
	}
	public static function toOptionArray() {
		return array (
				array (
						'value' => self::HOVER,
						'label' => Mage::helper('megamenu')->__( 'Hover' )
				),
				array (
						'value' => self::CLICK,
						'label' => Mage::helper('megamenu')->__( 'Click' )
				)
		);
	}
}