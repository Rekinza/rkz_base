<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_Positions extends Varien_Object {
	public static function getOptionArray() {
		$options = array ();
		foreach ( Mage::getModel ( 'megamenu/position' )->getCollection () as $pos ) {
			$options [$pos->getTitle ()] = $pos->getTitle ();
		}
		return $options;
	}
	public static function toOptionArray() {
		$options [] = array (
				'value' => '',
				'label' => Mage::helper('megamenu')->__( '--Please Select--' )
		);
		foreach ( Mage::getModel ( 'megamenu/position' )->getCollection () as $pos ) {
			$options [] = array (
					'value' => $pos->getId (),
					'label' => $pos->getTitle ()
			);
		}
		return $options;
	}
}