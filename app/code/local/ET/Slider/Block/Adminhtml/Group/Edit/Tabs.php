<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct();
		$this->setId('group_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle( Mage::helper('slider')->__('Group Information') );
	}
	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
				'label' => Mage::helper('slider')->__('Group Information'),
				'title' => Mage::helper('slider')->__('Group Information'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_group_edit_tab_form')->toHtml()
		));
		
		$this->addTab('slider_section', array(
				'label' => Mage::helper('slider')->__('Slider Options'),
				'title' => Mage::helper('slider')->__('Slider Options'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_group_edit_tab_slider')->toHtml()
		));
		
		$this->addTab('navigation_section', array(
				'label' => Mage::helper('slider')->__('Navigation Options'),
				'title' => Mage::helper('slider')->__('Navigation Options'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_group_edit_tab_navigation')->toHtml()
		));
		
		$this->addTab('slideshow_section', array(
				'label' => Mage::helper('slider')->__('Slideshow Options'),
				'title' => Mage::helper('slider')->__('Slideshow Options'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_group_edit_tab_slideshow')->toHtml()
		));
		
		$this->addTab('caption_section', array(
				'label' => Mage::helper('slider')->__('Caption Options'),
				'title' => Mage::helper('slider')->__('Caption Options'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_group_edit_tab_caption')->toHtml()
		));
		
		return parent::_beforeToHtml();
	}
}