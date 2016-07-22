<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Block_Panel extends Mage_Core_Block_Template {
	
	public function getForm(){
		$helper = Mage::helper('oxynic');
		
		$form = new Varien_Data_Form ( array (
				'id' => 'user_setting',
				'action' => $this->getUrl ( '*/*/save' ),
				'method' => 'post'
		));
		
		$form->addField('theme_color', 'select', array(
			'label'     => Mage::helper('megamenu')->__('Theme Color'),
			'name'      => 'theme_color',
			'value'		=> $helper->getConfig('theme_color', 'default')
		));
		
		return $form;
	}
	
}
