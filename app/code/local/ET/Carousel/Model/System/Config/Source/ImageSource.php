<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Model_System_Config_Source_ImageSource extends Varien_Object {
    public function toOptionArray() {
    	$options = array();
    	$attributes = Mage::getSingleton('catalog/config')->getAttributesUsedInProductListing();
    	foreach ($attributes as $value => $attr){
    		if ( 'media_image' == $attr->getData('frontend_input') ){
    			$options[] = array(
    				'value' => $value,
    				'label' => $attr->getFrontendLabel()
    			);
    		}
    	}
    	return $options;
    }
}
