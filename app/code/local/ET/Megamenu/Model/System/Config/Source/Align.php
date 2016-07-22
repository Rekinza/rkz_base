<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Align extends Varien_Object {
	const DEF = 0;
	const LEFT = 1;
	const RIGHT = 2;
	public static function getOptionArray() {
		return array (
			self::DEF => Mage::helper('megamenu')->__( 'Default' ),
			self::LEFT => Mage::helper('megamenu')->__( 'Left' ),
			self::RIGHT => Mage::helper('megamenu')->__( 'Right' )
		);
	}
	public static function toOptionArray() {
		return array (
				array (
						'value' => self::DEF,
						'label' => Mage::helper('megamenu')->__( 'Default' )
				),
				array (
						'value' => self::LEFT,
						'label' => Mage::helper('megamenu')->__( 'Left' )
				),
				array (
						'value' => self::RIGHT,
						'label' => Mage::helper('megamenu')->__( 'Right' )
				)
		);
	}
}