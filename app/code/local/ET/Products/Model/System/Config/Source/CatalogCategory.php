<?php
/**
 * @package ET_Products
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Products_Model_System_Config_Source_CatalogCategory {
	
	const ALL_CATEGORIES_PATH_FILTER = '^1(/[0-9]+){1,}$';
	
	public function toOptionArray($addEmpty = true) {
		$options = array();
		
		/* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
		$collection = Mage::getResourceModel('catalog/category_collection')
			->addAttributeToSelect('name')
			->addPathFilter(self::ALL_CATEGORIES_PATH_FILTER)
			->addAttributeToSort('path', 'ASC')
			->load();
		
		foreach ($collection as $category) {
			$prefix = $category->path;
			$prefix = preg_replace('#^1\/[0-9]+#','', $prefix);
			$prefix = preg_replace('#\/[0-9]+#', '- - ', $prefix);
			
			$option = array(
				'label' => $prefix . $category->getName(),
				'value' => $category->getId()
			);
			array_push($options, $option);
		}
		
		return $options;
		
	}

}