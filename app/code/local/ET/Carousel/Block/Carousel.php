<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Block_Carousel extends Mage_Catalog_Block_Product_Abstract {
    
    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_productCollection = null;
    
    protected $_template = 'et/carousel/default.phtml';
    
    public function __construct ($attributes=array()) {
        parent::__construct ($attributes);
        $this->_initConfig($attributes);
    }
    
    protected function _initConfig($attributes=array()){
        if (!$this->getData('config')){
            if ($store_config = Mage::getStoreConfig('et_carousel_configs')) {
                $_config = new Varien_Object();
                foreach ($store_config as $group => $fields) {
                    foreach ($fields as $name => $value) {
                        $this->setData($name, $value);
                    }
                }
            }
            is_array($attributes) && $this->addData($attributes);
            $this->setData('config', true);
        }
        return $this;
    }
    
    public function setConfig ($name, $value=null) {
        if (is_array($name)) {
            $this->addData($name);
        } else if(!empty($name)) {
            $this->setData($name, $value);
        }
        return $this;
    }
    
    public function getConfig ($name=null, $default=null) {
        if (is_null($name)) {
            return $this->getData();
        } else {
            return $this->getDataSetDefault($name, $default);
        }
    }
    
    public function getConfigFlag($name, $df=false){
        $flag = $this->getConfig($name, $df);
        return (!empty($flag) && 'false' !== $flag) ? true : false;
    }
    
    public function getItems () {
        $data_source = $this->getConfig('data_source', 'catalog_category');
        // TODO: check $data_source is valid
        return $this->_getItems($data_source);
    }
    
    protected function _getProductCollection () {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('catalog/product_collection');
            $this->_productCollection
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
				->addAttributeToFilter("special_price",array("gt"=>0))
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite()
                ->addStoreFilter();
        }
        return $this->_productCollection;
    }
    
    /**
     *
     * @param array $categoryIds id of categories
     * @param string $regex Pattern to find category path
     * @param boolean $load Load and return array instead of Collection
     * @return Mage_Catalog_Model_Resource_Category_Collection|array:
     */
    protected function _getCategories ($categoryIds='*', $regex='^1/[0-9/]+', $load=false) {
        if (!empty($categoryIds)) {
            /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
            $collection = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('*')
                ->addIsActiveFilter();
            
            if ($categoryIds != '*') {
                $collection->addIdFilter($categoryIds);
            }
            
            if (!is_null($regex)) {
                $collection->addPathFilter($regex);
            }
            
            if (!$load) return $collection;
            return $collection->getItems();
        }
        return array();
    }
    
    /**
     *
     * @param array $categoryIds
     * @param int $max_depth
     * @param boolean $sepa
     * @return array
     */
    protected function _getChildrenCategories ($categoryIds = array(), $max_depth=0, $sepa=false) {
        $children = array();
        if (!is_array($categoryIds)) settype($categoryIds, 'array');
        if (!is_array($categoryIds) || $max_depth<1) return $categoryIds;
        foreach ($categoryIds as $category) {
            // get sub category, limit by max_depth
            if (! $category instanceof Mage_Catalog_Model_Category) {
                $category = Mage::getModel('catalog/category')->load((int)$category);
                if (!$category->getId()) continue;
            }
            $child_ids = array($category->getId());
            if ($max_depth) {
                $query = '^' . $category->path . "(/[0-9]+){0,$max_depth}$";
                $_collection = $this->_getCategories('*', $query, 0);
                if ($_collection instanceof Mage_Catalog_Model_Resource_Category_Collection) {
                    $child_ids = $_collection->getAllIds();
                }
            }
            if ($sepa) {
                $children[$category->getId()] = $child_ids;
            } else {
                $children = array_merge($children, $child_ids);
            }
        }
        $children = array_unique($children);
        return $children;
    }
    
    protected function _getItems ($data_source = null) {
        /* @var $helper ET_Carousel_Helper_Data */
        $helper = Mage::helper('carousel');
        $collection = array();
        
        if ($data_source == null) {
            $data_source = $this->getConfig('data_source', 'catalog_category');
        }
        switch ($data_source) {
            default:
            case 'catalog_category':
                $collection = $this->_getProductCollection();
                
                $category_ids = $this->getConfig('catalog_category');
                if ($category_ids != '*') {
                    if (is_string($category_ids)) {
                        $category_ids = explode(',', $category_ids);
                    }
                    $child_category_include = $this->getConfig('child_category_include', 0);
                    $include_max_depth = $this->getConfig('include_max_depth', 0);
                    if ($child_category_include) {
                        $category_ids = $this->_getChildrenCategories($category_ids, $include_max_depth, false);
                    }
                    $collection->inCategoryFilter($category_ids);
                }
                $helper->isNewFilter($collection, $this->getConfig('is_new_filter', 0));
                $helper->isSpecialFilter($collection, $this->getConfig('is_special_filter', 0));
                $helper->isFeaturedFilter($collection, $this->getConfig('is_featured', 0));
                
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
                // Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
                
                $order_by = $this->getConfig('order_by');
                $order_dir = $this->getConfig('order_dir', 'ASC');
                
                // SORT & position fallback
                $this->_setOrder($collection, $order_by, $order_dir);
                // $collection->addAttributeToSort('position', 'ASC');
                
                if ($page = $this->getConfig('page')) {
                    $collection->setCurPage($page);
                } else {
                    $collection->setCurPage(1);
                }
                
                if ($limit = $this->getConfig('product_count')) {
                    $collection->setPageSize($limit);
                }
                
                break;
            case 'array_serialized':
                $items = $this->getConfig('array_serialized', '');
                if ( !empty($items) ) {
                    $items = unserialize($items);
                }
                $collection = new Varien_Data_Collection();
                if ($items) {
                    foreach($items as $item) {
                        $item_id = ++$i;
                        $collection->addItem(new Varien_Object($item));
                    }
                }
                break;
            case 'product_skus':
                /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
                $collection = $this->_getProductCollection();
                
                $skus = $this->getConfig('product_skus');
                if (is_string($skus)) {
                    $skus = preg_split('/[\s|,|;]/', $skus, -1, PREG_SPLIT_NO_EMPTY) ;
                } else if (is_array($skus)) {
                    $skus = array_map('trim', $skus);
                }
                $skus = array_filter($skus, create_function('$s', '$_s = trim($s); return !empty($_s);'));
                
                if ($skus) {
                    $collection->addAttributeToFilter(
                        'sku', array('in' => $skus)
                    );
                }
                
                $helper->isNewFilter($collection, $this->getConfig('is_new_filter', 0));
                $helper->isSpecialFilter($collection, $this->getConfig('is_special_filter', 0));
                $helper->isFeaturedFilter($collection, $this->getConfig('is_featured', 0));
                
                Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
                
                $order_by = $this->getConfig('order_by');
                $order_dir = $this->getConfig('order_dir', 'ASC');
                
                // SORT & position fallback
                $this->_setOrder($collection, $order_by, $order_dir);
                // $collection->addAttributeToSort('position', 'ASC');
                
                if ($page = $this->getConfig('page')) {
                    $collection->setCurPage($page);
                } else {
                    $collection->setCurPage(1);
                }
                
                if ($limit = $this->getConfig('product_count')) {
                    $collection->setPageSize($limit);
                }
                
                break;
        }
        return $collection;
    }
    
    protected function _setOrder (& $collection, $order_by='position', $order_dir='ASC') {
        if (empty($order_dir)) $order_dir='ASC';
        switch ($order_by) {
            case 'created_at':
                $collection->addAttributeToSelect('created_at');
            default:
                $collection->addAttributeToSort($order_by, $order_dir);
                break;
            case 'top_sales':
                $this->_addCountOrdered($collection);
                $collection->getSelect()->order(new Zend_Db_Expr("$order_by $order_dir"));
                break;
            case 'top_views':
                $this->_addCountVisited($collection);
                $collection->getSelect()->order(new Zend_Db_Expr("$order_by $order_dir"));
                break;
            case 'top_rating':
            case 'top_reviews':
                $this->_addReviewsCount($collection);
                $collection->getSelect()->order(new Zend_Db_Expr("$order_by $order_dir"));
                break;
            case 'random':
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
                break;
        }
        
    }
    
    /**
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    protected function _addCountOrdered (& $collection) {
        $order_item_table = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
        $subquery = Mage::getSingleton('core/resource')->getConnection('core_read')
            ->select()
            ->from($order_item_table, array('product_id', 'top_sales' => 'SUM(`qty_ordered`)'))
            ->group('product_id');
            
        $collection->getSelect()->joinLeft(array('top_sales_subquery' => $subquery), 'top_sales_subquery.product_id = e.entity_id');
        return true;
    }
    
    /**
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     */
    protected function _addCountVisited (& $collection) {
        
        $reports_event_table = Mage::getSingleton('core/resource')->getTableName('reports/event');
        
        $subquery = Mage::getSingleton('core/resource')->getConnection('core_read')
            ->select()
            ->from($reports_event_table, array('*', 'top_views' => 'COUNT(`event_id`)'))
            ->where('event_type_id = 1')
            ->group('object_id');
        
        $collection->getSelect()->joinLeft(array('top_views_subquery' => $subquery), 'top_views_subquery.object_id = e.entity_id');
        return true;
    }
    
    protected function _addReviewsCount (& $collection) {
        $review_summary_table = Mage::getSingleton('core/resource')->getTableName('review/review_aggregate');
        $collection->getSelect()
            ->joinLeft(
                array('rev' => $review_summary_table),
                'e.entity_id = rev.entity_pk_value AND rev.store_id=' . Mage::app()->getStore()->getId(),
                array(
                    'top_reviews' => 'rev.reviews_count',
                    'top_rating'  => 'rev.rating_summary'
                )
        );
        return true;
    }

}
