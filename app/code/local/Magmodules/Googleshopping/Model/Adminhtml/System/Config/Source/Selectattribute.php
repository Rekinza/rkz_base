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
 
class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Selectattribute {

	protected $_ignore = array(
		'color',
		'ebizmarts_mark_visited',
		'is_recurring',
		'links_purchased_separately',
		'price_view',
		'status',
		'tax_class_id',
		'visibility',
		'googleshopping_condition',
		'googleshopping_exclude',
		'shipment_type',		
	);
	
    public function toOptionArray(){
        $options = array();
		$options[] = array('value' => '', 'label' => Mage::helper('googleshopping')->__('-- none'));
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFilter('entity_type_id', $entityTypeId)->setOrder('attribute_code', 'ASC');
        foreach ($attributes as $attribute){
			if($attribute->getBackendType() == 'int') {
				if($attribute->getFrontendLabel()) {
					if(!in_array($attribute->getAttributeCode(), $this->_ignore)) {
						$options[] = array('value'=> $attribute->getAttributeCode(), 'label'=> $attribute->getFrontendLabel());				
					}
				}
			}
        }       
        return $options;
    }

}