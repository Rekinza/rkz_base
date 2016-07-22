<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Targets {
	const _SELF  = 0;
	const _BLANK = 1;
	const _POPUP = 2;
	public function toOptionArray() {
		return array (
				array (
						'value' => self::_SELF,
						'label' => Mage::helper('megamenu')->__( 'Same Window' )
				),
				array (
						'value' => self::_BLANK,
						'label' => Mage::helper('megamenu')->__( 'New Window' )
				),
				array (
						'value' => self::_POPUP,
						'label' => Mage::helper('megamenu')->__( 'Popup Window' )
				)
		);
	}
	public function getOptionArray() {
		return array (
				self::_SELF => Mage::helper('megamenu')->__( 'Same Window' ),
				self::_BLANK => Mage::helper('megamenu')->__( 'New Window' ),
				self::_POPUP => Mage::helper('megamenu')->__( 'Popup Window' )
		);
	}
}
