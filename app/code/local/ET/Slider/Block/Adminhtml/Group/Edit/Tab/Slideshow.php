<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tab_Slideshow extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getGroupData() ){
			$current = Mage::getSingleton('adminhtml/session')->getGroupData();
			Mage::getSingleton('adminhtml/session')->getGroupData(null);
		} elseif( Mage::registry('group_data') ) {
			$current = Mage::registry('group_data');
		}
		
		// check and set default values
		$slideshow_transitions_order = $current->getData('slideshow_transitions_order');
		if ( is_null($slideshow_transitions_order) ){
			$current->setData('slideshow_transitions_order', 1);
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('slideshow_opt_form', array('legend'=>Mage::helper('slider')->__('Slideshow Options')));
		
		$fieldset->addField('group_form_slideshow_transitions', 'multiselect', array(
				'label'     => Mage::helper('slider')->__('Transitions'),
				'name'      => 'slideshow_transitions',
				'value' 	=> $current->getData('slideshow_transitions'),
				'values'    => Mage::getModel('slider/system_config_source_transition_slide')->toOptionArray(true)
		));
		
		$fieldset->addField('group_form_slideshow_transitions_order', 'select', array(
				'label'     => Mage::helper('slider')->__('Transitions Order'),
				'name'      => 'slideshow_transitions_order',
				'value' 	=> $current->getData('slideshow_transitions_order'),
				'values'    => array(
						0 => array( 'value'=>0, 'label' => Mage::helper('slider')->__('Sequence')),
						1 => array( 'value'=>1, 'label' => Mage::helper('slider')->__('Random'))
				)
		));
		
		
// 		$fieldset->addField('group_form_slideshow_show_link', 'select', array(
// 				'label'     => Mage::helper('slider')->__('CSS ID'),
// 				'name'      => 'slideshow_show_link',
// 				'value'     => $current->getData('slideshow_show_link'),
// 				'values'    => array(
// 						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
// 						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') )
// 				)
// 		));
		
		return parent::_prepareForm();
	}
}