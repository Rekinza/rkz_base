<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_Layouts{
	public function toOptionArray(){
		return array(
			array('value' => 'full', 'label' => Mage::helper('oxynic')->__('Fluid')),
			array('value' => 'boxed', 'label' => Mage::helper('oxynic')->__('Boxed'))
		);
	}
}
