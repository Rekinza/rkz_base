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

class Magmodules_Googleshopping_Model_System_Config_Backend_Design_Shipping extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array {    

 	protected function _beforeSave() {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            if(count($value)){            	            	            	
            	$keys = array();            	
            	for($i=0; $i < count($value); $i++){
            		$keys[] = 'fields_' . uniqid();				
            	} 
				foreach($value as $key => $field){
					$price_from = str_replace(',','.',$field['price_from']);
					$price_to = str_replace(',','.',$field['price_to']);
					$price = str_replace(',','.',$field['price']);																			
					
					if(!$price_from) { $price_from = '0.00'; }
					if(!$price_to) { $price_to = '0.00'; }
					if(!$price) { $price = '0.00'; }

					$value[$key]['price_from'] = number_format($price_from, 2, '.', '');
					$value[$key]['price_to'] = number_format($price_to, 2, '.', '');
					$value[$key]['price'] = number_format($price, 2, '.', '');					
				}
				            	  				
				$value = array_combine($keys, array_values($value));            	
            }
        }        
        $this->setValue($value);
        parent::_beforeSave();
    }
    
}