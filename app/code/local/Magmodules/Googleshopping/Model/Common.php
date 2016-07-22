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
 
class Magmodules_Googleshopping_Model_Common extends Mage_Core_Helper_Abstract {

    public function getFileName($storeId, $name) {

        if(!$fileName = Mage::getStoreConfig($name . '/generate/filename', $storeId)) {
			$fileName = $name . '.xml';
		}
		
		if(substr($fileName, -3) != 'xml') {
			$fileName = $fileName . '-' . $storeId. '.xml';
		} else {
			$fileName = substr($fileName, 0, -4) . '-' . $storeId. '.xml';			
		}
		         
        if(!file_exists(Mage::getBaseDir('media') . DS . 'googleshopping')) {
        	mkdir(Mage::getBaseDir('media') . DS . 'googleshopping');
        }

		if(file_exists($filename)) {
            unlink($filename);
        }

        return Mage::getBaseDir() . DS . 'media' . DS . $name . DS . $fileName;
    }

	public function getValue($str) {
	
		return $str;
	}

	public function getXmlRow($attribute) {
		return "			<" . $attribute['name'] . ">" . $attribute['attribute'] . "</" . $attribute['name'] . ">\n";								
	}


	protected function getXmlClean($st, $striptags = true) {	
		if($striptags)
		$st = $this->stripTags($st);
		$st = str_replace(array("\r", "\n"), "", $st);
		$st = htmlspecialchars($st);
		$st = trim($st);
		return $st;
	}
		
	protected function getCategoryData($storeId) {	
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
		if($googleshopping_category = $eavAttribute->getIdByCode('catalog_category', 'googleshopping_category')) {
			$categories = Mage::getModel('catalog/category')->setStoreId($storeId)->getCollection()->addAttributeToSelect('id')->addAttributeToSelect('googleshopping_category');		
			$googleshopping_category = array();
			foreach($categories as $category) {			
				if($category->getGoogleshoppingCategory()) {
					$googleshopping_category[$category->getId()] = $category->getGoogleshoppingCategory();
				}
			}
			return $googleshopping_category;		
		}
		return false;	
	}    
    
}