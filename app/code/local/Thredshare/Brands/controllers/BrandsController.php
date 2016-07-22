<?php
date_default_timezone_set('Asia/Kolkata');
class Thredshare_Brands_BrandsController extends Mage_Core_Controller_Front_Action{
	
	public function getbrandsAction(){
	
		
		$this->loadLayout();          
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'newpage',
            array('template' => 'thredshare/brands/brands.phtml')
        );
		
		// GETTING DATA FROM ANOTHER METHOD
		
		$all_brands = Mage::getModel("thredshare_brands/brands")->fetchAllBrands();
 		
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle('Rekinza Brands');
		
		$block->setAllBrands($all_brands);
		
	
		$this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
	
	
	}
	
}

?>