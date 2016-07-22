<?php
/**
 * @package ET_Products
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Products_Model_System_Config_Source_DataSource {
    public function toOptionArray() {
        return array(
            array('value' => 'catalog_category', 'label' => Mage::helper('products')->__('Catalog')),
            array('value' => 'product_skus',     'label' => Mage::helper('products')->__('Specific Products'))
        );
    }
}
