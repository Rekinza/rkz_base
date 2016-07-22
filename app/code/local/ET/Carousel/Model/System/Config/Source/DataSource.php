<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Model_System_Config_Source_DataSource {
    public function toOptionArray() {
        return array(
            array('value' => 'catalog_category', 'label' => Mage::helper('carousel')->__('Catalog')),
            array('value' => 'product_skus',     'label' => Mage::helper('carousel')->__('Specific Products')),
            array('value' => 'array_serialized', 'label' => Mage::helper('carousel')->__('Customize'))
        );
    }
}
