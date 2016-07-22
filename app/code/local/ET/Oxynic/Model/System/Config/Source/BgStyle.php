<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_BgStyle {

    public function toOptionArray()
	{
		return array(
			array('value'=>'', 'label'=>Mage::helper('oxynic')->__('None')),
			array('value'=>'pattern', 'label'=>Mage::helper('oxynic')->__('Use Pattern')),
			array('value'=>'image', 'label'=>Mage::helper('oxynic')->__('Use Image'))
		);
	}
}
