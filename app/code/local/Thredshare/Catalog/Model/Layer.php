<?php
class Thredshare_Catalog_Model_Layer extends Mage_Catalog_Model_Layer{

 public function prepareProductCollection($collection)
    {
		
        parent::prepareProductCollection($collection);
		$collection->addAttributeToFilter("special_price",array("gt"=>0));
		
        return $this;
    }
}
?>
