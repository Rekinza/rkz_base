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

class Openwriter_Cartmart_Block_Vendor_List extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('cartmart/vendor/list.phtml');
        parent::__construct();
    }
    
    public function getVendorListCollection()
    {
		$userCollection = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1);		
				
		$userCollection->getSelect()->join(
			array('user_profile' => Mage::getSingleton('core/resource')->getTableName('cartmart/profile')), 
			'user_profile.user_id = main_table.user_id', array('user_profile.*')
		);		
		
        return $userCollection;
	}
	
	public function getProductList($vendor_id)
	{
		return Mage::getModel('catalog/product')->getCollection()->addFieldToFilter('vendor', $vendor_id)->addAttributeToFilter("special_price",array("gt"=>0))->setPageSize(4);
	}
	
	public function getProductImage($product_id)
	{
		$productModel = Mage::getModel('catalog/product')->load($product_id);			
		return Mage::helper('catalog/image')->init($productModel, 'small_image')->resize(91, 91);
	}
	
	public function getVendorProfileUrl($vendorId) 
	{
        return $this->getUrl('cartmart/vendor/profile', array('id' => $vendorId));
    }
    
    public function getViewAllProduct($vendorId)
    {
		return $this->getUrl('cartmart/vendor/items', array('id' => $vendorId));
	}
	
	public function getVendorImageUrl($vendorImageName)
	{
		$dirPath = 'vendor' . DS . 'images';
		return Mage::helper('cartmart')->getImagesUrl($dirPath) . $vendorImageName;
	}
}

?>
