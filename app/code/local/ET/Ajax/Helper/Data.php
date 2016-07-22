<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Ajax_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public function getQuickviewUrl($product = null){
		if (! $product instanceof Mage_Catalog_Model_Product ){
			return '';
		}
		if (! $product->getId() ){
			return '';
		}
		return $this->_getUrl('quickview/index/product', array(
			'id' => $product->getId()
		));
	}
	
	public function isNew($product = null){
		if (! $product instanceof Mage_Catalog_Model_Product ){
			return false;
		}
		if (! $product->getId() ){
			return false;
		}
		
		$todayStartOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		$todayEndOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		if ($product->getData('news_from_date') == null && $product->getData('news_to_date') == null) {
			return false;
		}
		
		if ($product->getData('news_from_date') !== null) {
			$product_nfd = $product->getData('news_from_date');
        	if ($product_nfd > $todayEndOfDayDate) {
            	return false;
        	}
	    }
	    
	    if ($product->getData('news_to_date') !== null) {
	    	$product_ntd = $product->getData('news_to_date');
	    	if ($product_ntd < $todayEndOfDayDate) {
	    		return false;
	    	}
	    }
	
   	 	return true;
	}
	
	public function isSpecial($product = null){
		if (! $product instanceof Mage_Catalog_Model_Product ){
			return false;
		}
		if (! $product->getId() ){
			return false;
		}
	
		$todayStartOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	
		$todayEndOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		
		if (!$product->getSpecialFromDate() && !$product->getSpecialToDate()) {
			return false;
		}
	
		if ($product->getSpecialFromDate() !== null) {
			$product_sfd = $product->getSpecialFromDate();
			if ($product_sfd > $todayEndOfDayDate) {
				return false;
			}
		}
		 
		if ($product->getSpecialToDate() !== null) {
			$product_std = $product->getSpecialToDate();
			if ($product_std < $todayEndOfDayDate) {
				return false;
			}
		}
	
		return true;
	}
	
	public function isEnabled(){
	    return Mage::getStoreConfigFlag('et_ajax_configs/general/is_enabled');
	}
	
	public function isQuickviewEnabled(){
		return $this->isEnabled() && Mage::getStoreConfigFlag('et_ajax_configs/general/is_ajax_quickview_enabled');
	}
	
	public function isAjaxCompareEnabled(){
		return $this->isEnabled() && Mage::getStoreConfigFlag('et_ajax_configs/general/is_ajax_compare_enabled');
	}
	
	public function isAjaxWishlistEnabled(){
		return $this->isEnabled() && Mage::getStoreConfigFlag('et_ajax_configs/general/is_ajax_wishlist_enabled');
	}
	
	public function isAjaxCheckoutEnabled(){
		return $this->isEnabled() && Mage::getStoreConfigFlag('et_ajax_configs/general/is_ajax_checkout_enabled');
	}
	
	public function getConfirmSize(){
	    $confirm_size = Mage::getStoreConfig('et_ajax_configs/general/confirm_size');
	    $size_w = intval($confirm_size);
	    $size_h = strpos($confirm_size, 'x')===false ? null : intval(substr($confirm_size, 1+strpos($confirm_size, 'x')));
	    $size_js = array();
	    if ($size_w){
	        $size_js[] = "width: '{$size_w}px'";
	    }
	    if ($size_h){
	        $size_js[] = "height: '{$size_h}px'";
	    }
	    return '{' . implode(', ', $size_js) . '}';
	}
	
	public function getLoadingSize(){
	    $loading_size = Mage::getStoreConfig('et_ajax_configs/general/loading_size');
	    $size_w = intval($loading_size);
	    $size_h = strpos($loading_size, 'x')===false ? null : intval(substr($loading_size, 1+strpos($loading_size, 'x')));
	    $size_js = array();
	    if ($size_w){
	        $size_js[] = "width: '{$size_w}px'";
	    }
	    if ($size_h){
	        $size_js[] = "height: '{$size_h}px'";
	    }
	    return '{' . implode(', ', $size_js) . '}';
	}
	
	public function getOptionsSize(){
	    $options_size = Mage::getStoreConfig('et_ajax_configs/general/options_size');
	    $size_w = intval($options_size);
	    $size_h = strpos($options_size, 'x')===false ? null : intval(substr($options_size, 1+strpos($options_size, 'x')));
	    $size_js = array();
	    if ($size_w){
	        $size_js[] = "width: '{$size_w}px'";
	    }
	    if ($size_h){
	        $size_js[] = "height: '{$size_h}px'";
	    }
	    return '{' . implode(', ', $size_js) . '}';
	}
	
	public function getQuickviewSize(){
	    $quickview_size = Mage::getStoreConfig('et_ajax_configs/general/quickview_size');
	    $size_w = intval($quickview_size);
	    $size_h = strpos($quickview_size, 'x')===false ? null : intval(substr($quickview_size, 1+strpos($quickview_size, 'x')));
	    $size_js = array();
	    if ($size_w){
	        $size_js[] = "width: '{$size_w}px'";
	    }
	    if ($size_h){
	        $size_js[] = "height: '{$size_h}px'";
	    }
	    return '{' . implode(', ', $size_js) . '}';
	}
	
	/**
	 *
	 * @param array $blocks names
	 * @param array $response ajax response array (ET standard)
	 */
	public function addUpdatesToResponse(array &$response=array(), array $blocks=array()){
	    $layout = Mage::app()->getLayout();
	    if ($blocks && is_array($blocks)){
	        if (!is_array($response)){
	            $response = array();
	        }
	        if (!isset($response['updates'])){
	            $response['updates'] = array();
	        }
	        foreach ($blocks as $name) {
	            $block = $layout->getBlock($name);
	            if ($block) {
	                $key = str_replace('.', '_', $name);
	                $response['updates'][$key] = $block->toHtml();
	            }
	        }
	    }
	    return $this;
	}
	
}
