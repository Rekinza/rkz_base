<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Caption_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$form = new Varien_Data_Form( array(
				'id' => 'edit_form',
				'action' => $this->getUrl('*/*/save', array(
						'id' => $this->getRequest()->getParam('id')
				) ),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
		) );
		
		$form->setUseContainer( true );
		$this->setForm( $form );
		return parent::_prepareForm();
	}
}