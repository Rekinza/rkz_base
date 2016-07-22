<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Captions {
	
	public static function getOptionArray() {
		$options = array ();
		foreach ( Mage::getModel('slider/caption')->getCollection() as $capt ) {
			$options[$capt->getId()] = $capt->getTitle();
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options[] = array (
			'value' => '',
			'label' => Mage::helper('slider')->__ ('--Please Select--')
		);
		foreach ( Mage::getModel('slider/caption')->getCollection() as $capt ) {
			$options[] = array (
				'value' => $capt->getId(),
				'label' => $capt->getTitle()
			);
		}
		return $options;
	}
}
