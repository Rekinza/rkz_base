<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Caption_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getCaptionData() ){
			$current = Mage::getSingleton('adminhtml/session')->getCaptionData();
			Mage::getSingleton('adminhtml/session')->getCaptionData(null);
		} elseif( Mage::registry('caption_data') ) {
			$current = Mage::registry('caption_data');
		}
		
		$form = new Varien_Data_Form();
		$this->setForm( $form );
		
		$fieldset = $form->addFieldset('caption_form', array('legend'=>Mage::helper('slider')->__('Group Attributes')));
		
		$state_field = $fieldset->addField('caption_form_state', 'select', array(
				'label'     => Mage::helper('slider')->__('State'),
				'name'      => 'state',
				'value' 	=> $current->getData('state'),
				'values'    => Mage::getModel('slider/system_config_source_state')->toOptionArray()
		));
		
		$title_field = $fieldset->addField('caption_form_title', 'text', array(
				'label'     => Mage::helper('slider')->__('Title'),
				'name'      => 'title',
				'class'     => 'required-entry',
				'required'  => true,
				'value'     => $current->getData('title')
		));
		
		$fieldset->addField('caption_form_description', 'textarea', array(
				'name' => 'description',
				'label' => Mage::helper('slider')->__( 'Description'),
				'title' => Mage::helper('slider')->__( 'Description'),
				'value' => $current->getData('description')
		));
		
		$fieldset->addField('caption_form_slide_id', 'select', array(
				'label'     => Mage::helper('slider')->__('Slide'),
				'name'      => 'slide_id',
				'class'     => 'required-entry',
				'required'  => true,
				'value' => $current->getData('slide_id'),
				'values'=> Mage::getModel('slider/system_config_source_slides')->toOptionArray()
		));
		
		$fieldset->addField('caption_form_caption_image', 'image', array(
				'label' => Mage::helper('slider')->__('Caption Image'),
				'name' => 'caption_image',
				'value' => $current->getData('caption_image')
		));
		
		// play in transition
		$fieldset->addField('caption_form_play_in', 'select', array(
				'label'     => Mage::helper('slider')->__('Transition In'),
				'name'      => 'play_in',
				'value' => $current->getData('play_in'),
				'values' => Mage::getModel('slider/system_config_source_transition_caption')->toOptionArray()
		));
		$fieldset->addField('caption_form_play_in_dur', 'text', array(
				'label'     => Mage::helper('slider')->__('Transition In Duration'),
				'name'      => 'play_in_dur',
				'value' => $current->getData('play_in_dur')
		));
		
		// play out transition
		$fieldset->addField('caption_form_play_out', 'select', array(
				'label'     => Mage::helper('slider')->__('Transition Out'),
				'name'      => 'play_out',
				'value' => $current->getData('play_out'),
				'values' => Mage::getModel('slider/system_config_source_transition_caption')->toOptionArray()
		));
		$fieldset->addField('caption_form_play_out_dur', 'text', array(
				'label'     => Mage::helper('slider')->__('Transition Out Duration'),
				'name'      => 'play_out_dur',
				'value' => $current->getData('play_out_dur')
		));
		
		// delay time
		$fieldset->addField('caption_form_delay', 'text', array(
				'label'     => Mage::helper('slider')->__('Delay'),
				'name'      => 'delay',
				'value' => $current->getData('delay')
		));
		
		$fieldset->addField('caption_form_css_classname', 'text', array(
				'label' => Mage::helper('slider')->__('CSS Class'),
				'name'  => 'css_classname',
				'value' => $current->getData('css_classname')
		));
		
		// use inline style?
		$field_inline_style = $fieldset->addField('slide_form_inline_style', 'select', array(
				'label'     => Mage::helper('slider')->__('Use Inline Style'),
				'name'      => 'inline_style',
				'value' => $current->getData('inline_style'),
				'values'=> array(
						array( 'value' => 0, 'label' => 'No' ),
						array( 'value' => 1, 'label' => 'Yes' )
				)
		));
		
		$field_css_position = $fieldset->addField('caption_form_css_position', 'select', array(
				'label'     => Mage::helper('slider')->__('CSS Position'),
				'name'      => 'css_position',
				'value' => $current->getData('css_position'),
				'values' => Mage::getModel('slider/system_config_source_css_position')->toOptionArray()
		));
		
		$field_css_top = $fieldset->addField('caption_form_css_top', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Top'),
				'name'      => 'css_top',
				'value' => $current->getData('css_top')
		));
		
		$field_css_left = $fieldset->addField('caption_form_css_left', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Left'),
				'name'      => 'css_left',
				'value' => $current->getData('css_left')
		));
		
		$field_css_width = $fieldset->addField('caption_form_css_width', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Width'),
				'name'      => 'css_width',
				'value' => $current->getData('css_width')
		));
		
		$field_css_height = $fieldset->addField('caption_form_css_height', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Height'),
				'name'      => 'css_height',
				'value' => $current->getData('css_height')
		));
		
		$field_css_overflow = $fieldset->addField('caption_form_css_overflow', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Overflow'),
				'name'      => 'css_overflow',
				'value' => $current->getData('css_overflow'),
				'values' => Mage::getModel('slider/system_config_source_css_overflow')->toOptionArray()
		));
		
		$this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
				->addFieldMap($field_inline_style->getHtmlId(), $field_inline_style->getName())
				->addFieldMap($field_css_position->getHtmlId(), $field_css_position->getName())
				->addFieldMap($field_css_top->getHtmlId(), $field_css_top->getName())
				->addFieldMap($field_css_left->getHtmlId(), $field_css_left->getName())
				->addFieldMap($field_css_width->getHtmlId(), $field_css_width->getName())
				->addFieldMap($field_css_height->getHtmlId(), $field_css_height->getName())
				->addFieldMap($field_css_overflow->getHtmlId(), $field_css_overflow->getName())
				->addFieldDependence($field_css_position->getName(), $field_inline_style->getName(), 1)
				->addFieldDependence($field_css_top->getName(), $field_inline_style->getName(), 1)
				->addFieldDependence($field_css_left->getName(), $field_inline_style->getName(), 1)
				->addFieldDependence($field_css_width->getName(), $field_inline_style->getName(), 1)
				->addFieldDependence($field_css_height->getName(), $field_inline_style->getName(), 1)
				->addFieldDependence($field_css_overflow->getName(), $field_inline_style->getName(), 1)
		);
		
		return parent::_prepareForm();
	}
}