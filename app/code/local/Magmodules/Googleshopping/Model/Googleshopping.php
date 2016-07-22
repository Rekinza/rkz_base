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
 
class Magmodules_Googleshopping_Model_Googleshopping extends Mage_Core_Helper_Abstract {


    public function getFileName($storeId) {

        if(!$fileName = Mage::getStoreConfig('googleshopping/generate/filename', $storeId)) {
			$fileName = 'googleshopping.xml';
		}
		
		if(substr($fileName, -3) != 'xml') {
			$fileName = $fileName . '-' . $storeId. '.xml';
		} else {
			$fileName = substr($fileName, 0, -4) . '-' . $storeId. '.xml';			
		}
		         
        return Mage::getBaseDir() . DS . 'media' . DS . 'googleshopping' . DS . $fileName;
    }
    
    
	public function generateFeed($storeId) {

		$time_start = microtime(true);
        $filename = $this->getFileName($storeId);

        if(!file_exists(Mage::getBaseDir('media') . DS . 'googleshopping')) {
        	mkdir(Mage::getBaseDir('media') . DS . 'googleshopping');
        }

		if(file_exists($filename)) {
            unlink($filename);
        }

		$total 			= 0;
		$category_data 	= $this->getCategoryData($storeId);	
		$websiteId 		= Mage::app()->getStore($storeId)->getWebsiteId();
        $websiteName 	= Mage::getModel('core/website')->load($websiteId)->getName();
        $websiteUrl 	= Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
		$mediaUrl		= Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$version 		= (string) Mage::getConfig()->getNode()->modules->Magmodules_Googleshopping->version;

		// Config Values
		$feed_config							= array();
		$feed_config['id'] 						= Mage::getStoreConfig('googleshopping/data/id', $storeId);
		$feed_config['name'] 					= Mage::getStoreConfig('googleshopping/data/name', $storeId);
		$feed_config['description'] 			= Mage::getStoreConfig('googleshopping/data/description', $storeId);
		$feed_config['condition_default'] 		= Mage::getStoreConfig('googleshopping/data/condition_default', $storeId);
		$feed_config['currency'] 				= Mage::app()->getStore($storeId)->getCurrentCurrencyCode(); 
		$feed_config['gtin_attribute'] 			= Mage::getStoreConfig('googleshopping/data/gtin_attribute', $storeId);
		$feed_config['brand_attribute'] 		= Mage::getStoreConfig('googleshopping/data/brand_attribute', $storeId);
		$feed_config['mpn_attribute'] 			= Mage::getStoreConfig('googleshopping/data/mpn_attribute', $storeId);
		$feed_config['category_fixed'] 			= Mage::getStoreConfig('googleshopping/data/category_fixed', $storeId);
		$feed_config['force_tax']	 			= Mage::getStoreConfig('googleshopping/advanced/force_tax', $storeId);
		$feed_config['add_tax']	 				= Mage::getStoreConfig('googleshopping/advanced/add_tax', $storeId);
		$feed_config['add_tax_perc']			= Mage::getStoreConfig('googleshopping/advanced/tax_percentage', $storeId);
		$feed_config['limit']	 				= Mage::getStoreConfig('googleshopping/generate/limit', $storeId);
		$feed_config['price_rules']	 			= Mage::getStoreConfig('googleshopping/advanced/price_rules', $storeId);
		$feed_config['identifier']	 			= Mage::getStoreConfig('googleshopping/advanced/identifier', $storeId);
		$feed_config['producttype']	 			= Mage::getStoreConfig('googleshopping/advanced/producttype', $storeId);
		$feed_config['extra_fields']			= @unserialize(Mage::getStoreConfig('googleshopping/advanced/extra', $storeId));
		$feed_config['shipping']				= @unserialize(Mage::getStoreConfig('googleshopping/advanced/shipping', $storeId));
		$feed_config['manage_stock']			= Mage::getStoreConfig('cataloginventory/item_options/manage_stock');
		$feed_config['weight']					= Mage::getStoreConfig('googleshopping/advanced/weight', $storeId);
		$feed_config['weight_units']			= Mage::getStoreConfig('googleshopping/advanced/weight_units', $storeId);
		$feed_config['price_scope']				= Mage::getStoreConfig('catalog/price/scope');
		$feed_config['stock'] 					= Mage::getStoreConfig('googleshopping/filter/stock', $storeId);					
		$feed_config['base_currency_code'] 		= Mage::app()->getStore($storeId)->getBaseCurrencyCode();
		$feed_config['current_currency_code']	= Mage::app()->getStore($storeId)->getCurrentCurrencyCode();					
		$feed_config['exchange_rate']			= 1;
		
		if($feed_config['base_currency_code'] != $feed_config['current_currency_code']) {
			$feed_config['exchange_rate'] = Mage::helper('directory')->currencyConvert(1, $feed_config['base_currency_code'], $feed_config['current_currency_code']);		
		}

		// Check if Attributes exists
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();		
		if($eavAttribute->getIdByCode('catalog_product', 'googleshopping_condition')) {
			$feed_config['googleshopping_condition_exists'] = 1;
		}
		if($eavAttribute->getIdByCode('catalog_product', 'googleshopping_exclude')) {
			$feed_config['googleshopping_exclude_exists'] = 1;
		}
				
		if($feed_config['add_tax'] && ($feed_config['add_tax_perc'] > 0)) {
			$add_tax_perc = 1 + ($feed_config['add_tax_perc'] / 100);			
		} else {
			$add_tax_perc = 1;
		}	
		
		$products = $this->getProducts($storeId, $feed_config);	
       	$xmlrows = array();
			
		// Google Feed
        $xml  = "<rss xmlns:g=\"http://base.google.com/ns/1.0\" version=\"2.0\">\n";
        $xml .= "	<channel>\n";
        $xml .= "		<title>" . $this->getXmlClean($websiteName, 'striptags') . "</title>\n";
        $xml .= "		<link>" .  $websiteUrl. "</link>\n";
      	$xml .= "		<description>" . $this->getXmlClean($websiteName, 'striptags') . " - Google Shopping Feed</description>\n";
		
		foreach($products as $product) {
		
			$identifiers = 0;
			
			if($feed_config['id'] != 'id') {
				if($product[$feed_config['id']]) {
					$xmlrows['g:id'] = $product[$feed_config['id']];
				} else {
					$xmlrows['g:id'] = $product->getId();				
				}
			} else {
				$xmlrows['g:id'] = $product->getId();
			}
			
			if($name = $product[$feed_config['name']]) {
				$xmlrows['g:title'] = $this->getXmlClean($name, 'stiptags_cdata');
			}	
						
			if($description = $product[$feed_config['description']]) {
				$description = trim(str_replace(array("\r", "\n"), "", $this->stripTags($description))); 
				$xmlrows['g:description'] = '<![CDATA[' . Mage::helper('core/string')->truncate($description, '1024') . ']]>';
			}	
			
			if($product->getUrlPath()) {
				$xmlrows['g:link'] = '<![CDATA[' . Mage::helper('core')->escapeHtml(trim($websiteUrl . $product->getUrlPath())). ']]>'; 
			} else {
				// EE FIX
				$url = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId())->getProductUrl();
				$url = explode('?', $url);
				$url = $url[0];
				$xmlrows['g:link'] = '<![CDATA[' . $url . ']]>'; 	
			}
		
			if($product->getImage()) {		
				if($product->getImage() != 'no_selection') {
					$productUrl = $mediaUrl . 'catalog/product' . $product->getImage(); 
					$xmlrows['g:image_link'] = '<![CDATA[' . $productUrl . ']]>'; 
				}
			}

			if((isset($feed_config['googleshopping_condition_exists'])) && ($condition = $product->getAttributeText('googleshopping_condition'))) {
				$xmlrows['g:condition'] = $condition;
			} else {
				$xmlrows['g:condition'] = Mage::helper('core')->escapeHtml($feed_config['condition_default']); 
			}
			
			$manage_stock = '';
			$outofstock = 0;
			
			if($product->getUseConfigManageStock()) {
				$manage_stock = $feed_config['manage_stock'];
			} else {
				$manage_stock = $product->getManageStock();			
			}	
			
			if($manage_stock) {
				if($product->getStockStatus()) {
					$xmlrows['g:availability'] = 'in stock';
				} else {
					$xmlrows['g:availability'] = 'out of stock';
					$outofstock = 1;					
				}
			} else {
				$xmlrows['g:availability'] = 'in stock';		
			}

			if($outofstock && $feed_config['stock']) {
				continue;
			}	

			if($feed_config['weight']) {
				$xmlrows['g:shipping_weight'] = $product->getWeight() . ' ' . $feed_config['weight_units'];
			}
			
			if($feed_config['price_scope'] == 1) {
				$_price = Mage::getResourceModel('catalog/product')->getAttributeRawValue($product->getId(), 'price', $storeId);
				$price = ($_price * $add_tax_perc * $feed_config['exchange_rate']);
			} else {
				$price = ($product->getPrice() * $add_tax_perc * $feed_config['exchange_rate']);
			}

			$pricerule_price = '';			
			if($feed_config['price_rules']) {
				$pricerule_price = Mage::getModel('catalogrule/rule')->calcProductPriceRule($product->setStoreId($storeId)->setCustomerGroupId(0), $product->getFinalPrice());
			} 

			$xmlrows['g:price'] = number_format($price, 2, '.', '') . ' ' . $feed_config['currency'];
		
			if($feed_config['force_tax']) {
				if($feed_config['force_tax'] == 'excl') {
					$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price, false) . ' ' . $feed_config['currency'];
				} else {
					$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price) . ' ' . $feed_config['currency'];				
				}	
			} else {
				$xmlrows['g:price'] = number_format($price, 2, '.', '') . ' ' . $feed_config['currency'];
			}

			$special_price = ''; $special_date = '';			
			if(($pricerule_price > 0) && ($pricerule_price < $product->getPrice())) {
				$price = ($pricerule_price * $add_tax_perc * $feed_config['exchange_rate']);
				if($feed_config['force_tax']) {
					if($feed_config['force_tax'] == 'excl') {
						$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price, false) . ' ' . $feed_config['currency'];
					} else {
						$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price) . ' ' . $feed_config['currency'];	
					}	
				} else {
					$xmlrows['g:sale_price'] = number_format($price, 2, '.', '') . ' ' . $feed_config['currency'];
				}	
			} else {			
				if($product->getSpecialPrice()) {
					$specialPriceFromDate = $product->getSpecialFromDate();
					$specialPriceToDate = $product->getSpecialToDate();
					$today = time();
					if($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime( $specialPriceFromDate) && is_null($specialPriceToDate)) {
						$price = ($product->getSpecialPrice() * $add_tax_perc * $feed_config['exchange_rate']);
						if($feed_config['force_tax']) {
							if($feed_config['force_tax'] == 'excl') {
								$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price, false) . ' ' . $feed_config['currency'];
							} else {
								$xmlrows['g:price'] = Mage::helper('tax')->getPrice($product, $price) . ' ' . $feed_config['currency'];	
							}	
						} else {
							$xmlrows['g:sale_price'] = number_format($price, 2, '.', '') . ' ' . $feed_config['currency'];						
						}
						if($specialPriceFromDate && $specialPriceToDate) {
							$xmlrows['g:sale_price_effective_date'] = str_replace(' ', 'T', $specialPriceFromDate) . '/' . str_replace(' ', 'T', $specialPriceToDate);
						}
					}
				}
			}

			if(($product->getTypeId() == 'bundle') && ($price < 0.01)) {
				$bundled_product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
				$xmlrows['g:price'] =  number_format(($bundled_product->getFinalPrice() * $feed_config['exchange_rate']), 2, '.', '') . ' ' . $feed_config['currency'];
			}		
						
			$xmlrows['g:gtin'] = Mage::helper('core')->escapeHtml($product[$feed_config['gtin_attribute']]);

			if($product[$feed_config['brand_attribute']]) {
				$xmlrows['g:brand'] = Mage::helper('core')->escapeHtml($product->getAttributeText($feed_config['brand_attribute'])); 
			}

			if($product[$feed_config['mpn_attribute']]) {
				if($product[$feed_config['mpn_attribute']]) {
					$xmlrows['g:mpn'] = Mage::helper('core')->escapeHtml($product[$feed_config['mpn_attribute']]); 
				} else {	
					$xmlrows['g:mpn'] = Mage::helper('core')->escapeHtml($product->getAttributeText($feed_config['mpn_attribute'])); 
				}	
			}															

			$google_category = $feed_config['category_fixed'];
			foreach($product->getCategoryIds() as $category_id) {
				if(array_key_exists($category_id, $category_data)) {
					$google_category = $category_data[$category_id];
				}	
			}

			if($google_category) {
				$xmlrows['g:google_product_category'] = Mage::helper('core')->escapeHtml($google_category);
			} 	

			if($feed_config['producttype']) {
				if($categoryIds = $product->getCategoryIds()) {
					$producttype = array();
					$categories = Mage::getModel('catalog/category')->getCollection()->setStoreId($storeId)->addAttributeToSelect('name')->addAttributeToFilter('entity_id', array('in'=> array($categoryIds)));
					foreach($categories as $_cat){
						$producttype[]= $_cat->getName();
					}
					if(count($producttype) > 1) {
						$producttypehtml = implode(' &gt; ', $producttype); 
					} else {
						$producttypehtml = $producttype[0];
					}											
					$xmlrows['g:product_type'] = $this->getXmlClean($producttypehtml, 'striptags');
				}
			}
			
			$identifier_xml = '';			
			if($feed_config['identifier'] == 1) {
				if(isset($xmlrows['g:gtin'])) { $identifiers++; }	
				if(isset($xmlrows['g:brand'])) { $identifiers++; }	
				if(isset($xmlrows['g:mpn'])) { $identifiers++; }									
				if($identifiers < 2) {
					$xmlrows['g:identifier_exists'] = 'FALSE';
				}								
			}
			
			if($feed_config['identifier'] == 2) {
				$xmlrows['g:identifier_exists'] = 'FALSE';
			}
			
			$extra_data = '';
			if(is_array($feed_config['extra_fields'])) {
				foreach($feed_config['extra_fields'] as $extra_attribute) {
					if(strlen($extra_attribute['name']) > 0) {
						if(($extra_attribute['type'] == 'int') || ($extra_attribute['type'] == 'boolean') || ($extra_attribute['type'] == 'select')) {
							$extra_data = $product->getAttributeText($extra_attribute['attribute']); 
						} 
						if(($extra_attribute['type'] == 'text') || ($extra_attribute['type'] == 'textarea')) {
							$extra_data = $product[$extra_attribute['attribute']];
						}					

						if($extra_attribute['type'] == 'multiselect') {
							$multiple = $product->getAttributeText($extra_attribute['attribute']);
							if(is_array($multiple)) {
								$extra_data = implode(',', $multiple);
							} else {						
								$extra_data = $product->getAttributeText($extra_attribute['attribute']); 
							}	
						}	
										
						if(!empty($extra_data)) {
							$xmlrows[$extra_attribute['name']] = $this->getXmlClean($extra_data, $extra_attribute['action']);											
						}
																			
						if($extra_attribute['attribute'] == 'entity_id') {
							$xmlrows[$extra_attribute['name']] = $product->getId();
						}
					
						if($extra_attribute['attribute'] == 'final_price') {
							if(isset($xmlrows['g:sale_price'])) {
								$xmlrows[$extra_attribute['name']] = $xmlrows['g:sale_price'];
							} else {
								$xmlrows[$extra_attribute['name']] = $xmlrows['g:price'];						
							}	
						}
					}					
				}
			}

			$shippingrows = '';

			foreach($feed_config['shipping'] as $shipping) {
				if(($price >= $shipping['price_from']) && ($price <= $shipping['price_to'])) {
				
					$shipping_price = $shipping['price'];
					$shipping_price = number_format($shipping_price, 2, '.', '') . ' ' . $feed_config['currency'];					
					
					$shippingrows .= "			<g:shipping>\n";		 
					$shippingrows .= "				<g:country>" . $shipping['country'] . "</g:country>\n";		 
					$shippingrows .= "				<g:service>" . $shipping['service'] . "</g:service>\n";		 
					$shippingrows .= "				<g:price>" . $shipping_price . "</g:price>\n";		 
					$shippingrows .= "			</g:shipping>\n";		 
					
				}				
			}	
									
			if(isset($feed_config['googleshopping_exclude_exists'])) {
				if($product->getGoogleshoppingExclude() == 1) {
					$xmlrows = '';
				}
			}
					
			if($xmlrows) {									
				$xml .= "		<item>\n";
				foreach($xmlrows as $key => $value) {
					$xml .= "			<" . $key . ">" .  $value . "</" . $key . ">\n";								
				}				
				if($shippingrows) {
					$xml .= $shippingrows;
				}
				$xml .= "		</item>\n";		
				unset($xmlrows);
				$total++;					
			}	
				
			if(($total >= $feed_config['limit']) && ($feed_config['limit'] > 0)) {
				break;
			}	
								
		}

		$time_end = microtime(true);
		$time = number_format(($time_end - $time_start), 4);
		$xml .= "	<!-- Products: ". $total . " -->\n";	
		$xml .= "	<!-- Generation time: ". $time . " -->\n";	
		$xml .= "	<!-- Generated at: ". Mage::getModel('core/date')->date('Y-m-d H:i:s') . " -->\n";	
		$xml .= "	<!-- Feed: Magmodules Googleshopping: ". $version . " -->\n";	
		$xml .= "	</channel>\n";
		$xml .= "</rss>\n";

        if (!file_put_contents($filename, $xml)) {
  			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('googleshopping')->__('File writing not succeeded'));
        } else {
        	$filename = explode('/googleshopping/', $filename);
        	$websiteUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        	$feed_url = $websiteUrl . 'googleshopping/' . $filename[1];			
			$result = array();
			$result['url'] = $feed_url;
			$result['shop'] = Mage::app()->getStore($storeId)->getCode();
			$result['date'] = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
			$result['qty'] = $total;
			$result['time'] = $time; 		
        	return $result;
        }

	}

    protected function getProducts($storeId, $feed_config) {
		$collection = Mage::helper('googleshopping')->getProductCollectionForStore($storeId);
		$collection->addAttributeToFilter('price', array('gt' => 0));        
		$collection->addAttributeToFilter('status', 1);        		
				
		if($visibility = Mage::getStoreConfig('googleshopping/filter/visibility_inc')) {
			if(strlen($visibility) > 1) {
				$visibility = explode(',', $visibility); 
				$collection->addAttributeToFilter('visibility', array('in' => array($visibility)));        		
			} else {
				$collection->addAttributeToFilter('visibility', array('eq' => array($visibility)));        					
			}	
		}        
		
		// All attributes
        $attributes = array(); 
        $attributes[] = 'url_path';
        $attributes[] = 'image';
        $attributes[] = 'price';
        $attributes[] = 'final_price';
        $attributes[] = 'special_price';
        $attributes[] = 'special_from_date';
        $attributes[] = 'special_to_date';        
        $attributes[] = 'type_id';                
        $attributes[] = 'tax_class_id';
        $attributes[] = 'tax_percent';
        $attributes[] = 'weight';
        $attributes[] = $feed_config['description'];
        $attributes[] = $feed_config['name'];
		
		if(isset($feed_config['id'])) {				
			if($feed_config['id'] != 'id') {			
        		$attributes[] = $feed_config['id'];
			}
		}	
        
		if(isset($feed_config['googleshopping_exclude_exists'])) {		
			$attributes[] = 'googleshopping_exclude';
		}
					
		if(isset($feed_config['googleshopping_condition_exists'])) {
			$attributes[] = 'googleshopping_condition';
		}
		
		if($feed_config['condition_default']) {
			$attributes[] = $feed_config['condition_default'];
		}
		if($feed_config['gtin_attribute']) {
			$attributes[] = $feed_config['gtin_attribute'];
		}
		if($feed_config['brand_attribute']) {
			$attributes[] = $feed_config['brand_attribute'];
		}
		if($feed_config['mpn_attribute']) {
			$attributes[] = $feed_config['mpn_attribute'];
		}
		
		if(is_array($feed_config['extra_fields'])) {
			foreach($feed_config['extra_fields'] as $extra_attribute) {
				$attributes[] = $extra_attribute['attribute'];
			}
		}
									                
        $collection->addAttributeToSelect($attributes);   
		
		if($limit = $feed_config['limit']) {
			if($limit > 0) {
				$collection->getSelect()->limit($limit);
			}	
		}
		
		$collection->joinTable('cataloginventory/stock_item', 'product_id=entity_id', array("stock_status" => "is_in_stock", "manage_stock" => "manage_stock", "use_config_manage_stock" => "use_config_manage_stock"))->addAttributeToSelect(array('stock_status', 'manage_stock', 'use_config_manage_stock'));		
        
        return $collection->load();
    }	

	protected function getXmlClean($st, $action = '') {	
		if($action) {
			$actions = explode('_', $action);
			if(in_array('striptags', $actions)) {			
				$st = $this->stripTags($st);
				$st = str_replace(array("\r", "\n"), "", $st);
				$st = htmlspecialchars($st);
				$st = trim($st);
			}	
			if(in_array('cdata', $actions)) {			
				$st = '<![CDATA[' . $st . ']]>';
			}	
		}	
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