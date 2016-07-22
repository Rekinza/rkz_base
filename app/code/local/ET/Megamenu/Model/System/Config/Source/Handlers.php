<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Handlers extends Varien_Object {
	const IS_NOTE = 0;
	const IS_DROPDOWN_CONTENT = 1;
	const IS_TITLE_REPLACER = 2;
	public static function getOptionArray() {
		return array (
				self::IS_NOTE => Mage::helper('megamenu')->__( 'As Note' ),
				self::IS_DROPDOWN_CONTENT => Mage::helper('megamenu')->__( 'As Dropdown Content' ),
				self::IS_TITLE_REPLACER => Mage::helper('megamenu')->__( 'Replace Title' )
		);
	}
	public static function toOptionArray() {
		return array (
				array (
						'value' => self::IS_NOTE,
						'label' => Mage::helper('megamenu')->__( 'As Note' )
				),
				array (
						'value' => self::IS_DROPDOWN_CONTENT,
						'label' => Mage::helper('megamenu')->__( 'As Dropdown' )
				),
				array (
						'value' => self::IS_TITLE_REPLACER,
						'label' => Mage::helper('megamenu')->__( 'Replace Title' )
				)
		);
	}
}