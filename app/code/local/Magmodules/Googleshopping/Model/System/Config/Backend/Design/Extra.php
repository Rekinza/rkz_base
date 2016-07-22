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

class Magmodules_Googleshopping_Model_System_Config_Backend_Design_Extra extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array {    

 	protected function _beforeSave() {
        $value = $this->getValue();
        if(is_array($value)) {
            unset($value['__empty']);

            if(count($value)) { 
            	$value = $this->orderData($value, 'name');

            	$keys = array();

            	for($i=0; $i < count($value); $i++){
            		$keys[] = 'fields_' . uniqid();
            	}   
				
				foreach($value as $key => $field){													
					$attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $field['attribute']);							
					$name = str_replace(" ", "_", trim($field['name']));					
					$value[$key]['name'] = strtolower($name);
					$value[$key]['attribute'] = $field['attribute'];				
					$value[$key]['action'] = $field['action'];				
					$value[$key]['type'] = $attribute->getFrontendInput();							
				}										
				$value = array_combine($keys, array_values($value));
            }
        }
        
        $this->setValue($value);
        parent::_beforeSave();
    }

	function orderData($data, $sort) { 
		$code = "return strnatcmp(\$a['$sort'], \$b['$sort']);"; 
		usort($data, create_function('$a,$b', $code)); 
		return $data; 
	} 
    
}