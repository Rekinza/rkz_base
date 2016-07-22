<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Block_Adminhtml_System_Config_Form_Field_Required extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element) {

        $element_name = $element->name;
        $element_path = $element->html_id;
        preg_match('#groups\[(.+)\]\[fields\]\[(.+)\]\[value\]#', $element_name, $keys);
        $attribute_code = null;
        if (count($keys)==3) {
            $attribute_code = $keys[2];
        }
        
        if ($attribute_code){
            // check the exist of the product attribute
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode('catalog_product', $attribute_code);
            if ( !$attribute->getId() ){
                return "Please add \"{$attribute_code}\" !";
            }
        }
        return parent::_getElementHtml($element);
    }
}