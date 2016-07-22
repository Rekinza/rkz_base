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
 
class Magmodules_Googleshopping_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getStoreIds() {
		$stores = Mage::app()->getStores();
		$storeids = array(); 
		foreach ($stores as $_store)  {
			$_storeId = Mage::app()->getStore($_store)->getId();
			if(Mage::getStoreConfig('googleshopping/general/enabled', $_storeId)) {
				$storeids[] = $_storeId;			
			}
		}
		return $storeids; 	
	}

	/*
	public function getAttributes($storeId) {
		$attributes	= array();
		$attributes[] = array('name' => 'g:title', 'attribute' => Mage::getStoreConfig('googleshopping/data/name', $storeId));
		$attributes[] = array('name' => 'g:description', 'attribute' => Mage::getStoreConfig('googleshopping/data/description', $storeId));
		$attributes[] = array('name' => 'g:gtin', 'attribute' => Mage::getStoreConfig('googleshopping/data/gtin_attribute', $storeId));
		$attributes[] = array('name' => 'g:brand', 'attribute' => Mage::getStoreConfig('googleshopping/data/brand_attribute', $storeId));
		$attributes[] = array('name' => 'g:mpn', 'attribute' => Mage::getStoreConfig('googleshopping/data/mpn_attribute', $storeId));
		$attributes[] = array('name' => 'g:condition', 'attribute' => Mage::getStoreConfig('googleshopping/data/condition_default', $storeId));
		return $attributes;	
	}	
	*/
	
	public function getProductCollectionForStore($store_id) {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->setStore($store_id);
        $collection->addStoreFilter($store_id);
		$collection->getSelect()->group('entity_id');

		if(Mage::getStoreConfig('googleshopping/filter/category_enabled', $store_id)) {			
			$type = Mage::getStoreConfig('googleshopping/filter/category_type', $store_id);
			$categories = Mage::getStoreConfig('googleshopping/filter/categories', $store_id);
			if($type && $categories) {
				$table = Mage::getSingleton('core/resource')->getTableName('catalog_category_product');
				if($type == 'include') {
					$collection->getSelect()->join(array('cats' => $table), 'cats.product_id = e.entity_id');
					$collection->getSelect()->where('cats.category_id in (' . $categories . ')');			
				} else {
					$collection->getSelect()->join(array('cats' => $table), 'cats.product_id = e.entity_id');
					$collection->getSelect()->where('cats.category_id not in (' . $categories . ')');
				}
			}
		}	
		
        return $collection;
    }
    	
}