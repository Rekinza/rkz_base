<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getGroupData() ){
			$current = Mage::getSingleton('adminhtml/session')->getGroupData();
			Mage::getSingleton('adminhtml/session')->getGroupData(null);
		} elseif( Mage::registry('group_data') ) {
			$current = Mage::registry('group_data');
		}
		
		// check and set default values
		$loading_screen = $current->getData('loading_screen');
		if ( is_null($loading_screen) ){
			$current->setData('loading_screen', 1);
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('group_form', array('legend'=>Mage::helper('slider')->__('General Attributes')));
		
		$state_field = $fieldset->addField('group_form_state', 'select', array(
				'label'     => Mage::helper('slider')->__('State'),
				'name'      => 'state',
				'value' 	=> $current->getData('state'),
				'values'    => Mage::getModel('slider/system_config_source_state')->toOptionArray()
		));
		
		$title_field = $fieldset->addField('group_form_title', 'text', array(
				'label'     => Mage::helper('slider')->__('Title'),
				'name'      => 'title',
				'class'     => 'required-entry',
				'required'  => true,
				'value' => $current->getData('title')
		));
		
		$fieldset->addField('group_form_description', 'textarea', array(
				'name' => 'description',
				'label' => Mage::helper('slider')->__( 'Description'),
				'title' => Mage::helper('slider')->__( 'Description'),
				'value' => $current->getData('description')
		));
		
		
		$fieldset->addField('group_form_css_id', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS ID'),
				'name'      => 'css_id',
				'value'     => $current->getData('css_id')
		));
		
		$fieldset->addField('group_form_css_position', 'select', array(
				'label'     => Mage::helper('slider')->__('CSS Position'),
				'name'      => 'css_position',
				'value'     => $current->getData('css_position'),
				'values'    => Mage::getModel('slider/system_config_source_css_position')->toOptionArray()
		));
		
		$fieldset->addField('group_form_css_width', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Width'),
				'name'      => 'css_width',
				'value'     => $current->getData('css_width')
		));
		
		$fieldset->addField('group_form_css_height', 'text', array(
				'label'     => Mage::helper('slider')->__('CSS Height'),
				'name'      => 'css_height',
				'value'     => $current->getData('css_height')
		));
		
		$fieldset->addField('group_form_loading_screen', 'select', array(
				'label'     => Mage::helper('slider')->__('Loading Screen'),
				'name'      => 'loading_screen',
				'value' => $current->getData('loading_screen'),
				'values'=> array(
						array( 'value' => 0, 'label' => 'No' ),
						array( 'value' => 1, 'label' => 'Yes' )
				)
		));
		
		if ( $current->getId() ){
			$static_helper_field = $fieldset->addField('group_form_static_helper', 'note', array(
					'label'     => Mage::helper('slider')->__('Static Block Usage'),
					'name'      => 'helper',
					'text' 		=> '<b>{{block type="slider/slider" group_id="'. $current->getId() .'"}}</b>',
					'comment' 	=> 'Copy and insert this code into your page.'
			));
			
			$layout_update = htmlentities('<block type="slider/slider" name="et.slider.default">').'<br/>';
			$layout_update .= htmlentities('    <action method="setGroupId">').'<br/>';
			$layout_update .= htmlentities('        <group_id>'.$current->getId().'</group_id>').'<br/>';
			$layout_update .= htmlentities('    </action>').'<br/>';
			$layout_update .= htmlentities('</block>').'<br/>';
			$layout_helper_field = $fieldset->addField('group_form_layout_helper', 'note', array(
					'label'     => Mage::helper('slider')->__('Layout Update Usage'),
					'name'      => 'helper',
					'text' 		=> '<pre><b>'.($layout_update).'</b></pre>',
					'comment' 	=> 'Copy and insert this code into your layout xml.'
			));
		}
		
		return parent::_prepareForm();
	}
}