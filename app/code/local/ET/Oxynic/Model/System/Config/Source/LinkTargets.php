<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_LinkTargets {
	public function toOptionArray() {
		return array(
				array('value'=>'_self',	'label'=>Mage::helper('oxynic')->__('Same Window')),
				array('value'=>'_blank','label'=>Mage::helper('oxynic')->__('New Window')),
				array('value'=>'_popup','label'=>Mage::helper('oxynic')->__('Popup Window'))
		);
	}
}
