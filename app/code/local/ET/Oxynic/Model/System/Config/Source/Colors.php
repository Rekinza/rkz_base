<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_Colors {
	public function toOptionArray(){
		return array(
			array('value' => 'default',      'label' => Mage::helper('oxynic')->__('Default')),
			array('value' => 'blue',         'label' => Mage::helper('oxynic')->__('Blue')),
			array('value' => 'fern',         'label' => Mage::helper('oxynic')->__('Fern')),
			array('value' => 'brow',         'label' => Mage::helper('oxynic')->__('Brow')),
			array('value' => 'orange',       'label' => Mage::helper('oxynic')->__('Orange')),
			array('value' => 'green',        'label' => Mage::helper('oxynic')->__('Green')),
			array('value' => 'yellow',       'label' => Mage::helper('oxynic')->__('Yellow')),
			array('value' => 'pink',         'label' => Mage::helper('oxynic')->__('Pink')),
			array('value' => 'lavender',     'label' => Mage::helper('oxynic')->__('Lavender')),
		);
	}
}
