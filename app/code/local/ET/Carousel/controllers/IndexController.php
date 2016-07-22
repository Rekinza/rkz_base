<?php
//HEADER_COMMENT//

class ET_Carousel_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction () {
		$this->loadLayout();
		
		$block = $this->getLayout()->getBlock('carousel.index');
        if ($block) {
            // $block->setTemplate('et/carousel/custom.phtml');
    		// $block->setConfig('data_source', 'catalog_category');
    		$block->setConfig('is_new_filter', rand(0,1000) % 3 );
    		$block->setConfig('is_special_filter', rand(0,1000) % 3 );
    		$block->setConfig('is_featured', rand(0,1000) % 3 );
    		$block->setConfig('product_count', rand(0,1000) % 5 + 5 );
    		$block->setConfig('order_by', $this->_getRandom(array(
    			'position',
    			'name',
               	'price',
               	'created_at',
               	'random',
               	'top_rating',
               	'top_reviews',
               	'top_views',
               	'top_sales'
    		)));
    		$block->setConfig('order_dir', $this->_getRandom(array(
    			'ASC',
    			'DESC'
    		)));
    		$block->setConfig('description_truncate', 40);
        }
		$this->renderLayout();
	}
	
	public function _getRandom($array = array()){
	    $size = count($array);
	    $array = array_slice($array, rand(0, $size-1));
	    return array_shift( $array );
	}
    
}