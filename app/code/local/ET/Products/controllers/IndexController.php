<?php
/**
 * @package ET_Products
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Products_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction () {
		$this->loadLayout();
		
		$block = $this->getLayout()->getBlock('products.index');
		if ($block) {
    		$block->setConfig('data_source', 'catalog_category');
    		
    		
//     		$block->setConfig('is_new_filter', rand(0,1000) % 3 );
//     		$block->setConfig('is_special_filter', rand(0,1000) % 3 );
//     		$block->setConfig('is_featured', rand(0,1000) % 3 );
//     		$block->setConfig('product_count', rand(0,1000) % 5 + 5 );

    		$block->setConfig('product_count', '' );
    		
    		$block->setConfig('order_by', $this->_getRandom(array(
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
    		
    		$block->setConfig('catalog_category', $this->_getRandom(array(
    		    '*',
    		    '4',
    		    '5',
    		    '6',
    		    '4,5',
    		    '5,6',
    		    '6,4',
    		    '4,5,6',
    		    ''
    		)));
    		$block->setConfig('child_category_include', rand(0,1000) % 2 );
    		$block->setConfig('include_max_depth', rand(0,1000) % 3 );
    		
    		$block->setConfig('col_lg', 4);
    		
    		
		}
		$this->renderLayout();
	}
	
	public function _getRandom($array = array()){
		$size = count($array);
		$array = array_slice($array, rand(0, $size-1));
		return array_shift( $array );
	}

    public function addIsFeaturedAttributeAction () {
    	Mage::helper('products')->createAttribute('is_featured', 'Is Featured', 'boolean', null)
    		&& Mage::helper('products')->insertAttributeToGroup('is_featured', 'General');
    	$this->getResponse()->setRedirect( Mage::getBaseUrl() );
    }
    public function removeIsFeaturedAttributeAction() {
    	$code = $this->getRequest()->getParam('code', 'is_featured');
    	Mage::helper('products')->removeAttributeByCode($code);
    	$this->getResponse()->setRedirect( Mage::getBaseUrl() );
    }
	
    public function addAttributeAction (){
    	$code = $this->getRequest()->getParam('code', 'is_featured');
    	$label = $this->getRequest()->getParam('label', 'Is_featured');
    	
    	Mage::helper('products')->createAttribute($code, 'Is Featured', 'boolean', null)
    		&& Mage::helper('products')->insertAttributeToGroup('is_featured', 'General');
    	$this->getResponse()->setRedirect( Mage::getBaseUrl() );
    }
	public function removeAttributeAction (){
		$code = $this->getRequest()->getParam('code', 'is_featured');
		Mage::helper('products')->removeAttributeByCode($code);
		$this->getResponse()->setRedirect( Mage::getBaseUrl() );
	}
	
	/**
	 * random dates for check is_new, is_special
	 * random is_featured values
	 */
	public function randomAction(){
		ob_start();
		$helper = Mage::helper('products');
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		/* @var $now Zend_Date */
		$now = Mage::app()->getLocale()->date();
		$count = array('new'=>0, 'special'=>0, 'featured'=>0);
		foreach ($collection as $product){
			
			if (rand(1,2)>1):
			
				$before = Mage::app()->getLocale()->date()->sub( rand(1, 10)*86400 );
				$after  = Mage::app()->getLocale()->date()->add( rand(1, 10)*86400 );
				if ( rand(1,10) < 3 ){
					$product->setData('news_from_date', $before);
					$product->setData('news_to_date', $after);
				} else {
					$product->setData('news_from_date', null);
					$product->setData('news_to_date', null);
				}
				if ( rand(1,10) < 3 ){
					$product->setData('special_from_date', $before);
					$product->setData('special_to_date', $after);
				} else {
					$product->setData('special_from_date', null);
					$product->setData('special_to_date', null);
				}
				if ( rand(1,10) < 3 ){
					$product->setData('is_featured', true);
				} else if (rand(1,10)>7) {
					$product->setData('is_featured', false);
				} else {
					$product->setData('is_featured', 0);
				}
				$product->save();
				
			endif;
			/* @var $product Varien_Object */
			
			echo '<br/>';
			echo '<br/>Product: ' . $product->getId();
			if ( $helper->isNew($product) ) : echo '<br/>IS NEW.'; ++$count['new']; endif;
			if ( $helper->isSpecial($product) ) : echo '<br/>IS SPECIAL.';  ++$count['special']; endif;
			if ( $helper->isFeatured($product) ) : echo '<br/>IS FEATURED.';  ++$count['featured']; endif;
			echo '<hr/>';
		}
		echo '<br/>';
		echo '<br/>Products: ' . $collection->getSize();
		echo '<br/>NEW: '.$count['new'];
		echo '<br/>SPECIAL: '.$count['special'];
		echo '<br/>FEATURED: '.$count['featured'];
		echo '<hr/>';
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
	
	/**
	 * Product status: is_new, is_special, is_featured
	 */
	public function statusAction(){
		ob_start();
		$helper = Mage::helper('products');
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		/* @var $now Zend_Date */
		$now = Mage::app()->getLocale()->date();
		
		$count = array('new'=>0, 'special'=>0, 'featured'=>0);
        echo '<link media="all" href="http://localhost/oxynic/skin/frontend/ecomtheme/oxynic/css/bootstrap.min.css" type="text/css" rel="stylesheet">';
		echo '<div class="well well-lg">';
        echo '<table class="table table-bordered">';
		echo '<thead>';
		echo '<tr>';
		echo '<td width="5%" align="left">ID</td>';
		echo '<td width="25%" align="left">Name</td>';
		echo '<td width="15%" align="center">News Date</td>';
		echo '<td width="5%" align="center"></td>';
		echo '<td width="15%" align="center">Special Date</td>';
		echo '<td width="5%" align="center"></td>';
		echo '<td width="5%" align="center"></td>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach ($collection as $product){
			$new_date_str = $product->getNewsFromDate() ? '' . $product->getNewsFromDate() : '';
			$new_date_str .= $product->getNewsToDate() ? '<br>'.$product->getNewsToDate() : '';
			if ( $helper->isNew($product) ) :
			    ++$count['new'];
			endif;
			
			$special_date_str = $product->getSpecialFromDate() ? '' . $product->getSpecialFromDate() : '';
			$special_date_str .= $product->getSpecialToDate() ? '<br>'.$product->getSpecialToDate() : '';
			if ( $helper->isSpecial($product) ) : ++$count['special']; endif;
			
			if ( $helper->isFeatured($product) ) : ++$count['featured']; endif;
			
			echo '<tr>';
			echo $this->__('<td>%s</td>', $product->getId());
			echo $this->__('<td>%s</td>', $product->getName() . " [{$product->getStoreId()}]" );
			echo $this->__('<td>%s</td>', $new_date_str);
			echo $this->__('<td>%s</td>', $helper->isNew($product) ? '<span class="label label-success">NEW</span>' : '' );
			echo $this->__('<td>%s</td>', $special_date_str);
			echo $this->__('<td>%s</td>', $helper->isSpecial($product) ? '<span class="label label-danger">SALE</span>' : '' );
			echo $this->__('<td>%s</td>', $helper->isFeatured($product) ? '<span class="label label-success">FEATURED</span>' : '' );
			echo '</tr>';
		}
		echo '</tbody>';
		echo '<tfoot>';
		echo '<tr>';
		echo '<td></td>';
		echo $this->__('<td>%s</td>', $collection->getSize());
		echo '<td></td>';
		echo $this->__('<td>%s</td>', $count['new']);
		echo '<td></td>';
		echo $this->__('<td>%s</td>', $count['special']);
		echo $this->__('<td>%s</td>', $count['featured']);
		echo '</tr>';
		echo '</tfoot>';
		echo '</table>';
		echo '</div>';
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
	
	/**
	 * random add catalog_product_view event, to test count_viewed
	 */
	public function fakeviewAction(){
		ob_start();
		$helper = Mage::helper('products');
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		
		/* @var $now Zend_Date */
		$now = Mage::app()->getLocale()->date();
		$count = array('new'=>0, 'special'=>0, 'featured'=>0);
		foreach ($collection as $product){
			
			
			if (rand(1,2)>1):
				$id = $product->getId();
				$num = rand(1, 100);
				while($num--){
					$sql ="INSERT INTO report_event (event_type_id, object_id, store_id, subtype) VALUES (1,$id, 1, 1);";
					Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' )->exec ( $sql );
				}
			endif;
		}
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
}