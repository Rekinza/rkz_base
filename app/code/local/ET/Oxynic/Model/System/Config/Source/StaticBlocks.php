<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_StaticBlocks{
	public function toOptionArray(){
		$staticBlocks = array();
		$staticBlocks[] = Mage::helper('adminhtml')->__('Please select static block ...');
		$cmsBlocksCollection = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('is_active', 1);
		foreach($cmsBlocksCollection as $key => $cmsBlock){
			$staticBlocks[$cmsBlock->getIdentifier()] = $cmsBlock->getTitle();
		}
		return $staticBlocks;
	}
}
