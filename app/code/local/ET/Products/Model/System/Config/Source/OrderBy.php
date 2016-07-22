<?php
/**
 * @package ET_Products
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Products_Model_System_Config_Source_OrderBy extends Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort{
    public function toOptionArray() {
        return array(
            array('value' => 'name',          'label' => Mage::helper('products')->__('Name')),
            array('value' => 'price',         'label' => Mage::helper('products')->__('Price')),
            array('value' => 'created_at',    'label' => Mage::helper('products')->__('Date Created')),
            array('value' => 'random',        'label' => Mage::helper('products')->__('RANDOM')),
            array('value' => 'top_rating',    'label' => Mage::helper('products')->__('Top Rating')),
            array('value' => 'top_reviews',   'label' => Mage::helper('products')->__('Top Reviews')),
            array('value' => 'top_views',     'label' => Mage::helper('products')->__('Top Views')),
            array('value' => 'top_sales',     'label' => Mage::helper('products')->__('Top Selling')),
        );
    }
}
