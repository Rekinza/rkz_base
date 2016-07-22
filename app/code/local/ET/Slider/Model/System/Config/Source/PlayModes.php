<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_PlayModes {
	public function toOptionArray() {
		return array(
			array('value' => '0',	'label' => Mage::helper('slider')->__('None (no play)')),
			array('value' => '1', 	'label' => Mage::helper('slider')->__('Chain (goes after main slide)')),
			array('value' => '3', 	'label' => Mage::helper('slider')->__('Chain Flatten (goes after main slide and flatten all caption animations)')),
		);
	}
}
