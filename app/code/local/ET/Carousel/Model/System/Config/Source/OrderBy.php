<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Model_System_Config_Source_OrderBy extends Mage_Adminhtml_Model_System_Config_Source_Catalog_ListSort{
    public function toOptionArray() {
        return array(
            array('value' => 'name',          'label' => Mage::helper('carousel')->__('Name')),
            array('value' => 'price',         'label' => Mage::helper('carousel')->__('Price')),
            array('value' => 'created_at',    'label' => Mage::helper('carousel')->__('Date Created')),
            array('value' => 'random',        'label' => Mage::helper('carousel')->__('Random')),
            array('value' => 'top_rating',    'label' => Mage::helper('carousel')->__('Top Rating')),
            array('value' => 'top_reviews',   'label' => Mage::helper('carousel')->__('Top Reviews')),
            array('value' => 'top_views',     'label' => Mage::helper('carousel')->__('Top Views')),
            array('value' => 'top_sales',     'label' => Mage::helper('carousel')->__('Top Selling')),
        );
    }
}
