<?php
/**
 * Openwriter Cartmart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Team
 * that is bundled with this package of Openwriter.
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Contact us Support does not guarantee correct work of this package
 * on any other Magento edition except Magento COMMUNITY edition.
 * =================================================================
 * 
 * @category    Openwriter
 * @package     Openwriter_Cartmart
**/
class Thredshare_Vendor_Block_List extends Mage_Catalog_Block_Product_List {

	public $_vendor;
	public function getVendorProductCollection()
	{
		$vendorProductCollection = $this->_getProductCollection();
				
		return $vendorProductCollection;
	}
	public function getAcceptedProductCollection()
	{
		$vendorProductCollection = $this->_getAcceptedProductCollection();
				
		return $vendorProductCollection;
	}
	public function getRejectedProductCollection()
	{
		$vendorProductCollection = $this->_getRejectedProductCollection();
				
		return $vendorProductCollection;
	}
	public function getSoldProductCollection()
	{
		$vendorProductCollection = $this->_getSoldProductCollection();
				
		return $vendorProductCollection;
	}
	
	protected function _getProductCollection()
    {
		$collection=null;
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$email=$customer->getEmail();
			//get corresponding vendor id
		
			$user = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1)->addFieldToFilter("email",$email)->getFirstItem();
		
			if ($user && $user->getUserId()){
			$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*');
			$collection->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor', $user->getUserId());
			}	
				
		
			}
	
	if (!$collection){
	$collection=parent::_getProductCollection()->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor',0);
	}
	    return $collection;
    }
	protected function _getAcceptedProductCollection()
    {
		$collection=null;
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$email=$customer->getEmail();
			//get corresponding vendor id
		
			$user = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1)->addFieldToFilter("email",$email)->getFirstItem();
		
			if ($user && $user->getUserId()){
			$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*');
			$collection->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor', $user->getUserId())->addAttributeToFilter("price",array("gt"=>0))
					->addAttributeToFilter("status",1)
					->joinField(
                        'is_in_stock',
                        'cataloginventory/stock_item',
                        'is_in_stock',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left'
                )->addAttributeToFilter('is_in_stock', array('eq' => 1));
					;
			}	
				
		
			}
	
	if (!$collection){
	$collection=parent::_getProductCollection()->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor',0);
	}
	    return $collection;
    }
	protected function _getRejectedProductCollection()
    {
		$collection=null;
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$email=$customer->getEmail();
			//get corresponding vendor id
		
			$user = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1)->addFieldToFilter("email",$email)->getFirstItem();
		
			if ($user && $user->getUserId()){
			$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*');
			$collection->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor', $user->getUserId())
					->addAttributeToFilter("status",2);
			}	
				
		
			}
	
	if (!$collection){
	$collection=parent::_getProductCollection()->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor',0);
	}
	    return $collection;
    }
	protected function _getSoldProductCollection()
    {
		$collection=null;
		if (Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$customer=Mage::getSingleton('customer/session')->getCustomer();
			$email=$customer->getEmail();
			//get corresponding vendor id
		
			$user = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1)->addFieldToFilter("email",$email)->getFirstItem();
		
			if ($user && $user->getUserId()){
			$collection = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('*');
			$collection->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor', $user->getUserId())
					->addAttributeToFilter('status',1)
					->joinField(
                        'is_in_stock',
                        'cataloginventory/stock_item',
                        'is_in_stock',
                        'product_id=entity_id',
                        '{{table}}.stock_id=1',
                        'left'
                )
                ->addAttributeToFilter('is_in_stock', array('eq' => 0));
			}	
				
		
			}
	
	if (!$collection){
	$collection=parent::_getProductCollection()->addAttributeToSelect('vendor')
					->addAttributeToFilter('vendor',0);
	}
	    return $collection;
    }
	public function getVendor(){
	if ($this->_vendor){
	return $this->_vendor;
	}
	$email=Mage::getSingleton("customer/session")->getCustomer()->getEmail();
	$this->_vendor=Mage::getModel('vendor/info')->getVendor($email);
	return $this->_vendor;
	}
	
	
}

?>
