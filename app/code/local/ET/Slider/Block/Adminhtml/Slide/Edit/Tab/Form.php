<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Slide_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getSlideData() ){
			$current = Mage::getSingleton('adminhtml/session')->getSlideData();
			Mage::getSingleton('adminhtml/session')->getSlideData(null);
		} elseif( Mage::registry('slide_data') ) {
			$current = Mage::registry('slide_data');
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('slide_form', array('legend'=>Mage::helper('slider')->__('Slide Attributes')));
		
		$state_field = $fieldset->addField('slide_form_state', 'select', array(
				'label'     => Mage::helper('slider')->__('State'),
				'name'      => 'state',
				'value' 	=> $current->getData('state'),
				'values'    => Mage::getModel('slider/system_config_source_state')->toOptionArray()
		));
		
		$title_field = $fieldset->addField('slide_form_title', 'text', array(
				'label'     => Mage::helper('slider')->__('Title'),
				'name'      => 'title',
				'class'     => 'required-entry',
				'required'  => true,
				'value' => $current->getData('title')
		));
		
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
		$fieldset->addField('slide_form_description', 'editor', array(
				'name' => 'description',
				'label' => Mage::helper('slider')->__( 'Description'),
				'title' => Mage::helper('slider')->__( 'Description'),
				'style' => 'width:700px; height:200px;',
				'config' => $wysiwygConfig,
				'value' => $current->getData('description')
		));
		
		$fieldset->addField('slide_form_group_id', 'select', array(
				'label'     => Mage::helper('slider')->__('Group'),
				'name'      => 'group_id',
				'class'     => 'required-entry',
				'required'  => true,
				'value' => $current->getData('group_id'),
				'values'=> Mage::getModel('slider/system_config_source_groups')->toOptionArray()
		));
		
		$fieldset->addField('slide_form_slide_image', 'image', array(
				'label' => Mage::helper('slider')->__('Slide Image'),
				'name' => 'slide_image',
				'value' => $current->getData('slide_image')
		));
		
		$fieldset->addField('slide_form_slide_image_lazyload', 'select', array(
				'label'     => Mage::helper('slider')->__('Lazyload'),
				'name'      => 'slide_image_lazyload',
				'value' => $current->getData('slide_image_lazyload'),
				'values'=> array(
						array( 'value' => 0, 'label' => 'No' ),
						array( 'value' => 1, 'label' => 'Yes' )
				)
		));
		
		$fieldset->addField('slide_form_slide_thumbnail', 'select', array(
				'label'     => Mage::helper('slider')->__('Thumbnail'),
				'name'      => 'slide_thumbnail',
				'value' => $current->getData('slide_thumbnail'),
				'values'=> array(
						array( 'value' => 0, 'label' => 'No' ),
						array( 'value' => 1, 'label' => 'Yes' )
				)
		));
		
		$fieldset->addField('slide_form_slide_transition', 'select', array(
				'label'     => Mage::helper('slider')->__('Slide Transition'),
				'name'      => 'slide_transition',
				'value' => $current->getData('slide_transition'),
				'values' => Mage::getModel('slider/system_config_source_transition_slide')->toOptionArray()
		));
		
		$fieldset->addField('slide_form_css_position', 'select', array(
				'label'     => Mage::helper('slider')->__('CSS Position'),
				'name'      => 'css_position',
				'value' => $current->getData('css_position'),
				'values' => Mage::getModel('slider/system_config_source_css_position')->toOptionArray()
		));
		
		$fieldset->addField('slide_form_css_top', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Top'),
				'name'      => 'css_top',
				'value' => $current->getData('css_top')
		));
		
		$fieldset->addField('slide_form_css_right', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Right'),
				'name'      => 'css_right',
				'value' => $current->getData('css_right')
		));
		
		$fieldset->addField('slide_form_css_bottom', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Bottom'),
				'name'      => 'css_bottom',
				'value' => $current->getData('css_bottom')
		));
		
		$fieldset->addField('slide_form_css_left', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Left'),
				'name'      => 'css_left',
				'value' => $current->getData('css_left')
		));
		
		$fieldset->addField('slide_form_css_width', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Width'),
				'name'      => 'css_width',
				'value' => $current->getData('css_width')
		));
		
		$fieldset->addField('slide_form_css_height', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Height'),
				'name'      => 'css_height',
				'value' => $current->getData('css_height')
		));
		
		$fieldset->addField('slide_form_css_overflow', 'select', array(
				'label'     => Mage::helper('slider')->__('CSS Overflow'),
				'name'      => 'css_overflow',
				'value' => $current->getData('css_overflow'),
				'values' => Mage::getModel('slider/system_config_source_css_overflow')->toOptionArray()
		));
		
		$fieldset->addField('slide_form_css_cursor', 'select', array(
				'label'     => Mage::helper('slider')->__('CSS Cursor'),
				'name'      => 'css_cursor',
				'value' => $current->getData('css_cursor'),
				'values' => Mage::getModel('slider/system_config_source_css_cursor')->toOptionArray()
		));
		
		return parent::_prepareForm();
	}
}