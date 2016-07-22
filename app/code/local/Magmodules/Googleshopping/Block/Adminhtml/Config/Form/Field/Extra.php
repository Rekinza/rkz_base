<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Googleshopping
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */

class Magmodules_Googleshopping_Block_Adminhtml_Config_Form_Field_Extra extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();
   	
    public function __construct() {        
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        
        $renderer_attribute = $layout->createBlock('googleshopping/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_attribute->setOptions(Mage::getModel('googleshopping/source_attribute')->toOptionArray());

        $renderer_action = $layout->createBlock('googleshopping/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_action->setOptions(Mage::getModel('googleshopping/source_action')->toOptionArray());

        $this->addColumn('name', array(
            'label' => Mage::helper('googleshopping')->__('Field Name'),
            'style' => 'width:120px',
        ));
        
        $this->addColumn('attribute', array(
            'label' => Mage::helper('googleshopping')->__('Attribute'),
            'style' => 'width:180px',
        	'renderer' => $renderer_attribute            
        ));   
        
        $this->addColumn('action', array(
            'label' => Mage::helper('googleshopping')->__('Actions'),
            'style' => 'width:120px',
        	'renderer' => $renderer_action
        ));

        $this->_renders['attribute'] = $renderer_attribute; 
        $this->_renders['action'] = $renderer_action; 
                             
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('googleshopping')->__('Add Field');
        parent::__construct();
    }
    
    protected function _prepareArrayRow(Varien_Object $row) {    	
    	foreach ($this->_renders as $key => $render){
	        $row->setData(
	            'option_extra_attr_' . $render->calcOptionHash($row->getData($key)),
	            'selected="selected"'
	        );
    	}
    } 

}