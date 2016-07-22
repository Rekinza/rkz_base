<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Slides {
	
	public static function getOptionArray() {
		$options = array ();
		foreach ( Mage::getModel('slider/slide')->getCollection() as $slid ) {
			$options[$slid->getId()] = $slid->getTitle();
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options[] = array (
			'value' => '',
			'label' => Mage::helper('slider')->__ ('--Please Select--')
		);
		foreach ( Mage::getModel('slider/slide')->getCollection() as $slid ) {
			$options[] = array (
				'value' => $slid->getId(),
				'label' => $slid->getTitle()
			);
		}
		return $options;
	}
}
