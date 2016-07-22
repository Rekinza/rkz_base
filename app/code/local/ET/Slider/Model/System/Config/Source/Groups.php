<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Groups {
	
	public static function getOptionArray() {
		$options = array ();
		foreach ( Mage::getModel('slider/group')->getCollection() as $grou ) {
			$options[$grou->getId()] = $capt->getTitle();
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options[] = array (
			'value' => '',
			'label' => Mage::helper('slider')->__ ('--Please Select--')
		);
		foreach ( Mage::getModel('slider/group')->getCollection() as $grou ) {
			$options[] = array (
				'value' => $grou->getId(),
				'label' => $grou->getTitle()
			);
		}
		return $options;
	}
}
