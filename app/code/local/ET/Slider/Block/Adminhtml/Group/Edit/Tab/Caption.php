<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tab_Caption extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getGroupData() ){
			$current = Mage::getSingleton('adminhtml/session')->getGroupData();
			Mage::getSingleton('adminhtml/session')->getGroupData(null);
		} elseif( Mage::registry('group_data') ) {
			$current = Mage::registry('group_data');
		}
		
		// check and set default values
		$caption_playin_mode = $current->getData('caption_playin_mode');
		if ( is_null($caption_playin_mode) ){
			$current->setData('caption_playin_mode', 1);
		}
		
		$caption_playout_mode = $current->getData('caption_playout_mode');
		if ( is_null($caption_playout_mode) ){
			$current->setData('caption_playout_mode', 1);
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('caption_opt_form', array('legend'=>Mage::helper('slider')->__('Caption Options')));
		
// 		$caption_class_field = $fieldset->addField('group_form_caption_class', 'select', array(
// 				'label'     => Mage::helper('slider')->__('Caption Class'),
// 				'name'      => 'state',
// 				'value' 	=> $current->getData('state'),
// 				'values'    => array(
// 						0 => array(
// 							'value' => '$JssorCaptionSlider$',
// 							'label' => '$JssorCaptionSlider$'
// 						)
// 				)
// 		));
		
		$fieldset->addField('group_form_caption_transitions', 'multiselect', array(
				'label'     => Mage::helper('slider')->__('Transitions'),
				'name'      => 'caption_transitions',
				'value'     => $current->getData('caption_transitions'),
				'values'    => Mage::getModel('slider/system_config_source_transition_caption')->toOptionArray(true)
		));
		
		$fieldset->addField('group_form_caption_playin_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('Play In Mode'),
				'name'      => 'caption_playin_mode',
				'value'     => $current->getData('caption_playin_mode'),
				'values'    => Mage::getModel('slider/system_config_source_playModes')->toOptionArray()
		));
		
		$fieldset->addField('group_form_caption_playout_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('Play Out Mode'),
				'name'      => 'caption_playout_mode',
				'value'     => $current->getData('caption_playout_mode'),
				'values'    => Mage::getModel('slider/system_config_source_playModes')->toOptionArray()
		));
		
		return parent::_prepareForm();
	}
}