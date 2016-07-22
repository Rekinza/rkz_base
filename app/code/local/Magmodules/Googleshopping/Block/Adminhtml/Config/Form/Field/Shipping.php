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

class Magmodules_Googleshopping_Block_Adminhtml_Config_Form_Field_Shipping extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

	protected $_renders = array();
   	
    public function __construct() {        
        $layout = Mage::app()->getFrontController()->getAction()->getLayout();
        
        $renderer_coutries = $layout->createBlock('googleshopping/adminhtml_config_form_renderer_select', '', array('is_render_to_js_template' => true));                							                
        $renderer_coutries->setOptions(Mage::getModel('googleshopping/source_countries')->toOptionArray());

        $this->addColumn('country', array(
            'label' => Mage::helper('googleshopping')->__('Country'),
            'style' => 'width:120px',
        	'renderer' => $renderer_coutries
        ));
        
        $this->addColumn('service', array(
            'label' => Mage::helper('googleshopping')->__('Service'),
            'style' => 'width:120px',
        ));
        
        $this->addColumn('price_from', array(
            'label' => Mage::helper('googleshopping')->__('Price From'),
            'style' => 'width:60px',
        ));
        $this->addColumn('price_to', array(
            'label' => Mage::helper('googleshopping')->__('Price To'),
            'style' => 'width:60px',
        ));
        
        $this->addColumn('price', array(
            'label' => Mage::helper('googleshopping')->__('Price'),
            'style' => 'width:60px',
        ));

        $this->_renders['country'] = $renderer_coutries; 
                             
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('googleshopping')->__('Add Shipping');
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