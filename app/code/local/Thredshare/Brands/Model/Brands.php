<?php
class Thredshare_Brands_Model_Brands extends Mage_Core_Model_Abstract{
	
	public function _construct(){
	
		$this->_init("thredshare_brands/brands");

	}
	
	public function fetchAllBrands()
	{

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT entity_name FROM sku_code_mapping WHERE type = 'brand' ORDER BY entity_name";
		$results = $readConnection->fetchAll($query);

		return $results;

	}
	
}

?>