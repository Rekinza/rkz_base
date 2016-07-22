<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_CssRepeat {

    public function toOptionArray()
	{
		return array(
			array('value'=>'repeat', 'label'=>Mage::helper('oxynic')->__('repeat')),
			array('value'=>'repeat-x', 'label'=>Mage::helper('oxynic')->__('repeat-x')),
			array('value'=>'repeat-y', 'label'=>Mage::helper('oxynic')->__('repeat-y')),
			array('value'=>'no-repeat', 'label'=>Mage::helper('oxynic')->__('no-repeat'))
		);
	}
}
