<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Size extends Varien_Object {
	public static function getOptionArray() {
		return array (
				'0' => Mage::helper('megamenu')->__( '0' ),
				'1' => Mage::helper('megamenu')->__( '1' ),
				'2' => Mage::helper('megamenu')->__( '2' ),
				'3' => Mage::helper('megamenu')->__( '3' ),
				'4' => Mage::helper('megamenu')->__( '4' ),
				'5' => Mage::helper('megamenu')->__( '5' ),
				'6' => Mage::helper('megamenu')->__( '6' ),
				'7' => Mage::helper('megamenu')->__( '7' ),
				'8' => Mage::helper('megamenu')->__( '8' ),
				'9' => Mage::helper('megamenu')->__( '9' ),
				'10' => Mage::helper('megamenu')->__( '10' ),
				'11' => Mage::helper('megamenu')->__( '11' ),
				'12' => Mage::helper('megamenu')->__( '12' )
		);
	}
	public static function toOptionArray() {
		return array (
				array (
						'value' => '0',
						'label' => Mage::helper('megamenu')->__( '0' )
				),
				array (
						'value' => '1',
						'label' => Mage::helper('megamenu')->__( '1' )
				),
				array (
						'value' => '2',
						'label' => Mage::helper('megamenu')->__( '2' )
				),
				array (
						'value' => '3',
						'label' => Mage::helper('megamenu')->__( '3' )
				),
				array (
						'value' => '4',
						'label' => Mage::helper('megamenu')->__( '4' )
				),
				array (
						'value' => '5',
						'label' => Mage::helper('megamenu')->__( '5' )
				),
				array (
						'value' => '6',
						'label' => Mage::helper('megamenu')->__( '6' )
				),
				array (
						'value' => '7',
						'label' => Mage::helper('megamenu')->__( '7' )
				),
				array (
						'value' => '8',
						'label' => Mage::helper('megamenu')->__( '8' )
				),
				array (
						'value' => '9',
						'label' => Mage::helper('megamenu')->__( '9' )
				),
				array (
						'value' => '10',
						'label' => Mage::helper('megamenu')->__( '10' )
				),
				array (
						'value' => '11',
						'label' => Mage::helper('megamenu')->__( '11' )
				),
				array (
						'value' => '12',
						'label' => Mage::helper('megamenu')->__( '12' )
				)
		);
	}
}