<?php
class Thredshare_Catalog_Model_Config extends Mage_Catalog_Model_Config
{
    public function getAttributeUsedForSortByArray()
    {	
	
        $sortArray=array_merge(
			parent::getAttributeUsedForSortByArray(),
			array('popularity' => Mage::helper('catalog')->__('Popularity')),
			array('new' => Mage::helper('catalog')->__('Newest'))
		);

	return $sortArray;
		
    }
}
?>