<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_OrderBy {
	public function toOptionArray() {
		return array(
            array('value' => 'name',          'label' => Mage::helper('oxynic')->__('Name')),
            array('value' => 'price',         'label' => Mage::helper('oxynic')->__('Price')),
            array('value' => 'created_at',    'label' => Mage::helper('oxynic')->__('Date Created')),
            array('value' => 'random',        'label' => Mage::helper('oxynic')->__('RANDOM')),
            array('value' => 'top_rating',    'label' => Mage::helper('oxynic')->__('Top Rating')),
            array('value' => 'top_reviews',   'label' => Mage::helper('oxynic')->__('Top Reviews')),
            array('value' => 'top_views',     'label' => Mage::helper('oxynic')->__('Top Views')),
            array('value' => 'top_sales',     'label' => Mage::helper('oxynic')->__('Top Selling')),
        );
	}
}
