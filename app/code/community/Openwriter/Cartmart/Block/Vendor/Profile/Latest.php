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
class Openwriter_Cartmart_Block_Vendor_Profile_Latest extends Mage_Catalog_Block_Product_Abstract {

    protected $_itemCollection = null;
	
	
    public function getItems() {
        $profileId = $this->getRequest()->getParam('id');
		$after=$this->getRequest()->getParam("after");
		if (!$after){
		$after=0;
		}
        $vendorId = Mage::getModel('cartmart/profile')->load($profileId)->getUserId();

        if (is_null($this->_itemCollection)) {
            $this->_itemCollection = Mage::getModel('cartmart/profile_products')->getItemsCollectionForVendorPage($vendorId,$after);
        }

        return $this->_itemCollection;
    }
    
    public function getHighestSellingProduct()
    {
		$profileId = $this->getRequest()->getParam('id');

        $vendorId = Mage::getModel('cartmart/profile')->load($profileId)->getUserId();
        
        $productIds = Mage::getModel('catalog/product')->getCollection()
			->addFieldToFilter('status', 1)
			->addAttributeToFilter('vendor', $vendorId)
			->getAllIds();
			
		if(empty($productIds))
			return null;		
		
		$productReportCollection = Mage::getResourceModel('reports/product_collection')
			->addOrderedQty()
			->addAttributeToSelect('*')
			->setOrder('ordered_qty', 'desc')
			->addFieldToFilter('entity_id', array('in', $productIds));		
		
		if($productReportCollection->count() > 0)
			return Mage::getModel('catalog/product')->load($productReportCollection->getFirstItem()->getId());
		else
			return null;		
	}
	
	
		
	public function getFollowers(){
		$profileId = $this->getRequest()->getParam('id');

        $vendor = Mage::getModel('cartmart/profile')->load($profileId);
		$favorites=json_decode($vendor->getFavourites());
		if (is_array($favorites) && count($favorites)){
		
			$query="select * from openwriter_cartmart_profile as c join  admin_user as a on c.user_id=a.user_id join customer_entity as b on a.email=b.email where b.entity_id in (".implode(",",$favorites).")";

			$read_connection=Mage::getSingleton("core/resource")->getConnection("core_read");
			$results=$read_connection->fetchAll($query);
			return $results;
		
		}
		return null;
	}
	
	public function getFollowing(){
	   $profileCollection = Mage::getModel('cartmart/profile')->getCollection();

        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $profiles = array();
        foreach ($profileCollection as $profile) {
            $favorites = $profile->getFavourites();

            if (!is_null($favorites) && !empty($favorites)) {
                $favorites = json_decode($favorites, true);
                if (in_array($customerId, $favorites))
                    $profiles[] = $profile->getId();
            }
        }
        
        $userIds = Mage::getModel('admin/user')->getCollection()
			->addFieldToFilter('is_active', 1)
			->getAllIds();

        $profileCollection = Mage::getModel('cartmart/profile')->getCollection()
                ->addFieldToFilter('entity_id', array('in' => $profiles))
                ->addFieldToFilter('user_id', array('in' => $userIds));

        return $profileCollection;
 
	}
	
	public function getNextPageUrl(){
		$profileId = $this->getRequest()->getParam('id');
		 $vendorId = Mage::getModel('cartmart/profile')->load($profileId)->getUserId();
		$after=$this->getRequest()->getParam("after");
		if (!$after){
		$after=0;
		}
	
		$collection=Mage::getModel('cartmart/profile_products')->getItemsCollectionForVendorPage($vendorId,$after+1);

		if ($collection->getSize()>0){
		$after++;
		return Mage::getUrl("*/*/*")."id/$profileId?after=".$after;
		}
		else{
		return -1;
		}
	
	}
}

?>
