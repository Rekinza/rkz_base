<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Slide_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct();
		$this->setId('slide_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle( Mage::helper('slider')->__('Slide Information') );
	}
	protected function _beforeToHtml() {
		$this->addTab('form_section', array(
				'label' => Mage::helper('slider')->__('Properties'),
				'title' => Mage::helper('slider')->__('Properties'),
				'content' => $this->getLayout()->createBlock('slider/adminhtml_slide_edit_tab_form')->toHtml()
		));
		
		return parent::_beforeToHtml();
	}
}