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
 
class Magmodules_Googleshopping_Model_Source_Attribute {

	public function toOptionArray() {
		
		$optionArray = array(); 
        
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
			->addFieldToFilter('backend_type', array('text', 'select', 'textarea', 'date', 'int', 'boolean', 'static', 'varchar'));

		// Some Default Attributes
		$optionArray[] = array('label' => Mage::helper('googleshopping')->__('- Product ID'), 'value' => 'entity_id');
		$optionArray[] = array('label' => Mage::helper('googleshopping')->__('- Final Price'), 'value' => 'final_price');
 
        foreach($attributes as $attribute) {
            $optionArray[] = array(
                'label' => str_replace("'", "", $attribute->getData('frontend_label')),
                'value' => $attribute->getData('attribute_code')
            );
        }
		
        return $optionArray;
	}
	
}