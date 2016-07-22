<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Caption_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	public function __construct() {
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'slider';
		$this->_controller = 'adminhtml_caption';
		
		$this->_updateButton('save', 'label', Mage::helper('slider')->__('Save Caption') );
		$this->_updateButton('delete', 'label', Mage::helper('slider')->__('Delete Caption') );
		
		$this->_addButton('saveandcontinue', array(
				'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
				'onclick' => 'saveAndContinueEdit()',
				'class' => 'save'
		), - 100 );
		
		$this->_formScripts [] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
	}
	
	public function getHeaderText() {
		if(Mage::registry('caption_data') && Mage::registry('caption_data')->getId()) {
			return Mage::helper('slider')->__("Edit Caption '%s'", $this->htmlEscape( Mage::registry('caption_data')->getTitle() ) );
		} else {
			return Mage::helper('slider')->__('New Caption');
		}
	}
	
	protected function _prepareLayout() {
		// Load Wysiwyg on demand and Prepare layout
		if(Mage::getSingleton('cms/wysiwyg_config')->isEnabled() &&($block = $this->getLayout()->getBlock('head'))) {
			$block->setCanLoadTinyMce(true);
		}
		parent::_prepareLayout();
	}
}