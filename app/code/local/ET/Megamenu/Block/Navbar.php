<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Navbar extends Mage_Core_Block_Template {
	
	protected $_template = 'et/megamenu/navbar.phtml';
	protected $_position = null;
	protected $_menus = null;
	
	private $_toplevel_menus = null;
	private $_by_parents = array();
	
	public function __construct() {
	    parent::__construct();
	    $this->addData(array(
	        'cache_lifetime'    => 86400,
	        'cache_tags'        => array('megamenu_navbar'),
	    ));
	}
	
	public function getPosition(){
		if (is_null($this->_position)){
		    $position_id = $this->getPositionId();
		    if ($position_id===false){
		        return false;
		    }
		}
		return $this->_position;
	}
	
	public function getPositionId(){
	    if (isset($this->_position)) {
	        return $this->_position->getId();
	    }
	    if ($this->hasData('position_id')) {
	        $position_id = $this->getData('position_id');
	        if ($position_id) {
    	        $position = Mage::getModel('megamenu/position')->load($position_id);
    	        if ($position_id == $position->getId()){
    	            $this->_position = $position;
    	            return $position_id;
    	        }
	        }
	    }
	    $default_position_id = Mage::getStoreConfig('et_megamenu_configs/general/position_id');
	    if ($default_position_id) {
	        $position = Mage::getModel('megamenu/position')->load($default_position_id);
	        if ($default_position_id == $position->getId()){
	            $this->_position = $position;
	            return $default_position_id;
	        }
	    }
	    
	    /* @var $collection ET_Megamenu_Model_Resource_Position_Collection */
	    $collection = Mage::getModel('megamenu/position')->getCollection();
	    $position = $collection->getFirstItem();
	    if ($position->getId()) {
	        $this->_position = $position;
	        return $position->getId();
	    }
		return false;
	}
	
	public function _load(){
		if (is_null($this->_menus)){
		    $position_id = $this->getPositionId();
		    if ($position_id){
    			$menus = Mage::getModel('megamenu/menu')->getCollection();
    			$menus->getSelect()
    				->where('main_table.lft > 0 AND main_table.position_id = ' . $this->getPositionId())
    				->where('main_table.state = 1')
    				->order('main_table.lft');
    			
    			$this->_menus = array();
    			foreach ( $menus->getItems() as $item ){
    				$this->_menus[$item->getId()] = $item;
    				$parent_id = $item->getParentId();
    				
    				if ( !isset($this->_by_parents[$parent_id]) ){
    					$this->_by_parents[$parent_id] = array();
    				}
    				$this->_by_parents[$parent_id][] = $item->getId();
    			}
		    } else {
		        $this->_menus = array();
		    }
		}
	}
	
	public function getTopLevelMenus(){
		if (is_null($this->_menus)){
			$this->_load();
		}
		if (is_null($this->_toplevel_menus)){
			$this->_toplevel_menus = array();
			foreach ($this->_menus as $id => $menu){
				if ($menu->getDepth() > 1){
					continue;
				}
				$this->_toplevel_menus[$menu->getId()] = &$this->_menus[$id];
			}
		}
		return $this->_toplevel_menus;
	}
	
	public function getParent($id){
		if ($id && isset($this->_menus[$id])){
			return $this->_menus[$id];
		}
		return null;
	}
	public function getChildren($id){
		if (is_null($this->_menus)){
			$this->_load();
		}
		if ($id && isset($this->_by_parents[$id])){
			$children = array();
			if ( count($this->_by_parents[$id]) ){
				foreach ($this->_by_parents[$id] as $child_id){
					$children[$child_id] = &$this->_menus[$child_id];
				}
			}
			return $children ? $children : false;
		}
		return false;
	}
	
	public function getMenuHtml($item=null){
		if (!is_null($item)){
			// $menu_block = $this->getLayout()->createBlock('megamenu/menu')->setData( $item->getData() )->setHelper($this);
			// return $menu_block->_toHtml();
			$menu_type = $item->getMenuType();
			if ( $menu_type == ET_Megamenu_Model_System_Config_Source_MenuType::DIVIDER ){
				return '<li class="menu-divider"></li>';
			}
			
			$is_dropdown = false;
			$show_link = true;
			$flink = '#';
			switch( $menu_type ){
				case ET_Megamenu_Model_System_Config_Source_MenuType::CATEGORY:
					$category_id = $item->getCategoryLink();
					if ( is_string($category_id) ){
						$category_id = str_replace('category/', '', $category_id);
						$category_id = intval($category_id);
					}
					$category = Mage::getModel('catalog/category')->setStoreId(Mage::app()->getStore()->getId())->load($category_id);
					if ( $category ){
						$flink = $category->getUrl();
						$classnames[] = 'category-'.$category_id;
						
						if ( $_category = Mage::registry('current_category') ){
							if ( $_category->getId() == $category->getId() ){
								$classnames[] = 'active';
							}
						}
					}
					break;
				case ET_Megamenu_Model_System_Config_Source_MenuType::PRODUCT:
					$product_id = $item->getProductLink();
					if ( is_string($product_id) ){
						$product_id = str_replace('product/', '', $product_id);
						$product_id = intval($product_id);
					}
					$product = Mage::getModel('catalog/product')->load($product_id);
					if ( $product ){
						$flink = $product->getProductUrl();
						$classnames[] = 'product-'.$product_id;
						
						if ( $_product = Mage::registry('current_product') ){
							if ( $_product->getId() == $product->getId() ){
								$classnames[] = 'active';
							}
						}
					}
					break;
				case ET_Megamenu_Model_System_Config_Source_MenuType::CONTENT:
					$dropdown_content = $item->dropdown_content;
					if ( $dropdown_content > 0 ){
						$is_dropdown = true;
					}
					if ( $dropdown_content > 1 ){
						$show_link = false;
					}
					break;
				case ET_Megamenu_Model_System_Config_Source_MenuType::EXTERNAL:
					$flink = $item->external_link;
					break;
				case ET_Megamenu_Model_System_Config_Source_MenuType::PAGE:
					$cms_page_id = $item->getCmspageLink();
					if ( is_string($cms_page_id) ){
						$cms_page_id = str_replace('cms_page/', '', $cms_page_id);
						$cms_page_id = intval($cms_page_id);
					}
					if ( $cms_page_id ){
						$home_identifier = Mage::getStoreConfig(Mage_Cms_Helper_Page::XML_PATH_HOME_PAGE);
						$page = Mage::getModel('cms/page')->load($cms_page_id);
						if ( $page->getIdentifier() == $home_identifier ){
							$flink = Mage::getBaseUrl();
						} else {
							$flink = Mage::helper('cms/page')->getPageUrl( $cms_page_id );
						}
						$classnames[] = 'page-'.$cms_page_id;
						
						$request_page_id = Mage::app()->getRequest()->getParam('page_id');
						if ( $request_page_id == $cms_page_id || (Mage::getSingleton('cms/page')->getIdentifier() == $home_identifier  && Mage::app()->getFrontController()->getRequest()->getRouteName() == Mage::getStoreConfig('web/default/front'))){
							$classnames[] = 'active';
						}
					}
					break;
			}
			if ($is_dropdown || ($item->getRgt() - $item->getLft() > 1)) {
				$classnames[] = 'parent';
			}
			$level = $item->getDepth();
			$ds = '';
			$cl = 0;
			if ($ds =  $item->getDropdownSize()){
				$cl = 1;
				if ( strpos($ds, '/')!==false ){
					$_ds = explode('/', $ds);
					$ds = $_ds[0];
					$cl = $_ds[1];
				}
				if ( preg_match('/px/', $ds) ){
					// $ds = intval($ds) . 'px';
				} else if ( $ds > 150 ){
					$ds = intval($ds) . 'px';
				} else if ($ds <= 12) {
					$cl = $ds;
					$ds = '';
				}
			}
    			
			if ('fullwidth' == $item->getDropdownLayout()) {
			    $classnames[] = 'dropdown-full';
			    $ds = '';
			}
    	    if ($level>1){
    			if ($ms = $item->getMenuSize()){
    				$classnames[] = 'mcol-'.$ms;
    			} else {
    				$classnames[] = 'mcol-1';
    			}
			}
			$classnames[] = 'level-'. $item->getDepth();
			
			ob_start();
			echo '<li class="'. implode(' ', $classnames) .'">';
			if ( $show_link ){
				echo '<a href="'.$flink.'">';
				if ($item->getTitle()) {
					echo '<span class="menu-title">'.$item->getTitle().'</span>';
				}
				if ($item->getSubtitle()) {
					echo '<span class="menu-subtitle">'.$item->getSubtitle().'</span>';
				}
				echo '</a>';
			}
			if ($is_dropdown) {
				echo '<div class="dropdown-menu">';
				echo '<div class="dropdown-content">';
				echo Mage::helper('cms')->getBlockTemplateProcessor()->filter($item->getDescription());
				echo '</div>';
				echo '</div>';
			} else if ($item->getRgt() - $item->getLft() > 1) {
				$children = $this->getChildren($item->getId());
				if ($children){
					echo '<div class="dropdown-menu" '.($ds?'style="width:'.$ds.';"':'').'>';
					echo '<ul '.($cl?'class="mcols-'.$cl.'"':'').'>';
					foreach ($children as $child){
						echo $this->getMenuHtml($child);
					}
					echo '</ul>';
					echo '</div>';
				}
			}
			echo '</li>';
			return ob_get_clean();
		}
		return '';
	}
	
	public function getMenus(){
	    $pos = $this->getPosition();
		if (is_null($this->_menus) && $pos){
			$menus = Mage::getModel('megamenu/menu')->getCollection();
			$menus->getSelect()
				->where('main_table.lft > 0 AND main_table.position_id = ' . $this->getPositionId())
				->where('main_table.state = 1')
				->order('main_table.lft');
			$this->_menus = $menus->getItems();
			
			$prev = null;
			$ignore_next = false;
			foreach ( $this->_menus as $current => $item ){
				if ( $ignore_next && $item->getDepth() > $ignore_next ){
					continue;
				} else {
					$ignore_next = false;
				}
				
				$item->is_parent = $item->getRgt() - $item->getLft() > 1;
				
				$classnames = array();
				$menu_type = $item->getMenuType();
				switch( $menu_type ){
					case ET_Megamenu_Model_System_Config_Source_MenuType::CATEGORY:
						$category_id = $item->getCategoryLink();
						if ( is_string($category_id) ){
							$category_id = str_replace('category/', '', $category_id);
							$category_id = intval($category_id);
						}
						$category = Mage::getModel('catalog/category')->load($category_id);
						if ( $category ){
							$item->flink = $category->getUrl();
							$classnames[] = 'category-'.$category_id;
						} else {
							$item->flink = '#';
						}
						break;
					case ET_Megamenu_Model_System_Config_Source_MenuType::PRODUCT:
						$product_id = $item->getProductLink();
						if ( is_string($product_id) ){
							$product_id = str_replace('product/', '', $product_id);
							$product_id = intval($product_id);
						}
						$product = Mage::getModel('catalog/product')->load($product_id);
						if ( $product ){
							$item->flink = $product->getProductUrl();
							$classnames[] = 'product-'.$product_id;
						} else {
							$item->flink = '#';
						}
						break;
					case ET_Megamenu_Model_System_Config_Source_MenuType::CONTENT:
						if ( $item->level == 1 && $item->dropdown_content == ET_Megamenu_Model_System_Config_Source_Handlers::IS_DROPDOWN_CONTENT ){
							$item->is_dropdown = true;
						}
						break;
					case ET_Megamenu_Model_System_Config_Source_MenuType::DIVIDER:
						$item->flink = '#';
						$item->is_parent = false;
						if ( $item->getDepth() > 1 ){
							$ignore_next = $item->getDepth();
						}
						break;
					case ET_Megamenu_Model_System_Config_Source_MenuType::EXTERNAL:
						$item->flink = $item->external_link;
						break;
					case ET_Megamenu_Model_System_Config_Source_MenuType::PAGE:
						$cms_page_id = $item->getCmspageLink();
						if ( is_string($cms_page_id) ){
							$cms_page_id = str_replace('cms_page/', '', $cms_page_id);
							$cms_page_id = intval($cms_page_id);
						}
						if ( $cms_page_id ){
							$item->flink = Mage::helper('cms/page')->getPageUrl( $cms_page_id );
							$classnames[] = 'page-'.$cms_page_id;
						} else {
							$item->flink = '#';
						}
						break;
				}
				
				if ($ms = $item->getMenuSize()){
					$classnames[] = 'mcol-'.$ms;
				} else {
					$classnames[] = 'mcol-1';
				}

				if ( ($dg = $item->getDropdownLayout()) && $item->is_parent ){
					$classnames[] = 'dropdown-'.$dg;
				}
				
				//var_dump( $item->getData() );
				$classnames[] = 'level-'. $item->getDepth();
				
				
				$item->classnames = $classnames;
				
				$item->deeper     = false;
				$item->shallower  = false;
				$item->level_diff = 0;
				if ( isset($prev) ){
					$prev->deeper     = $item->depth > $prev->depth;
					$prev->shallower  = $item->depth < $prev->depth;
					$prev->level_diff = $prev->depth - $item->depth;
				}
				$prev = $this->_menus[$current];
			}
			if (isset($prev)){
				$prev->deeper     = 1 > $prev->depth;
				$prev->shallower  = 1 < $prev->depth;
				$prev->level_diff = ($prev->depth - 1);
			}

			//die;
		}
		
		return $this->_menus;
	}
	
	public function getCacheKeyInfo() {
	    return array(
	        Mage::app()->getStore()->getCode(),
	        $this->getTemplateFile(),
	        'template' => $this->getTemplate(),
	        'ET_MEGAMENU_NAVBAR_BLOCK_TPL',
	    );
	}
}
