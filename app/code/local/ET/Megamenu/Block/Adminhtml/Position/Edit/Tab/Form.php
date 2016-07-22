<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Position_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		if ( !($form = $this->getForm()) ){
			$form = new Varien_Data_Form();
		}
		$this->setForm( $form );
		
		$fieldset = $form->addFieldset( 'position_form', array (
				'legend' => Mage::helper('megamenu')->__( 'Position information' )
		));
		
		$fieldset->addField( 'title', 'text', array (
				'label' => Mage::helper('megamenu')->__( 'Title' ),
				'class' => 'required-entry',
				'required' => true,
				'name' => 'title'
		));
		
		$fieldset->addField( 'state', 'select', array (
				'label' => Mage::helper('megamenu')->__( 'State' ),
				'name' => 'state',
				'values' => ET_Megamenu_Model_System_Config_Source_State::toOptionArray()
		));
		
		$fieldset->addField( 'description', 'editor', array (
				'name' => 'description',
				'label' => Mage::helper('megamenu')->__( 'Description' ),
				'title' => Mage::helper('megamenu')->__( 'Description' ),
				'style' => 'width:600px; height:200px;',
				'wysiwyg' => true
		));
		
		if (Mage::getSingleton( 'adminhtml/session' )->getPositionData()) {
			$form->setValues( Mage::getSingleton( 'adminhtml/session' )->getPositionData() );
			Mage::getSingleton( 'adminhtml/session' )->getPositionData ( null );
		} elseif (Mage::registry( 'position_data' )) {
			$form->setValues( Mage::registry( 'position_data' )->getData() );
		}
		
		return parent::_prepareForm ();
	}
}