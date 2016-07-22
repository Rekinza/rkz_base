<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Slider extends Mage_Core_Block_Template {
	
	public $_template = 'et/slider/default.phtml';
	public $_group = null;
	public $_slides = array();
	public $_captions = array();
	
	public function __construct() {
	    parent::__construct();
	    $this->addData(array(
	        'cache_lifetime'    => 86400,
	        'cache_tags'        => array('slider_slider'),
	    ));
	}
	
	public function _prepareLayout() {
		$head = $this->getLayout()->getBlock('head');
		$head->addItem('skin_js', 'et/slider/js/jssor.slider.min.js');
		return parent::_prepareLayout();
	}

    public function getGroup(){
		if (is_null($this->_group)){
		    $group_id = $this->getGroupId();
		    if ($group_id===false){
		        return false;
		    }
		    return Mage::getModel('slider/group')->load($group_id);
		}
		return $this->_group;
	}
	
	public function getGroupId(){
	    if (!is_null($this->_group)){
	        return $this->_group->getId();
	    }
	    if ($this->hasData('group_id')){
	        $group_id = $this->getData('group_id');
	        $has_group = Mage::getModel('slider/group')
	            ->getCollection()
	            ->addFieldToFilter('id', $group_id)
	            ->addFieldToFilter('state', ET_Slider_Model_System_Config_Source_State::STATE_PUBLISHED)
	            ->getSize();
	        if ($has_group) {
	            return $group_id;
	        }
	    }
	    $default_group_id = Mage::getStoreConfig('et_slider_configs/general/group_id');
	    $has_group = Mage::getModel('slider/group')
	        ->getCollection()
	        ->addFieldToFilter('id', $default_group_id)
	        ->addFieldToFilter('state', ET_Slider_Model_System_Config_Source_State::STATE_PUBLISHED)
	        ->getSize();
	    if ($has_group) {
	        $this->setData('group_id', $default_group_id);
	        return $default_group_id;
	    }
	    
	    /* @var $collection ET_Megamenu_Model_Resource_Group_Collection */
	    $collection = Mage::getModel('slider/group')
	        ->getCollection()
	        ->addFieldToFilter('state', ET_Slider_Model_System_Config_Source_State::STATE_PUBLISHED);
	    if ($collection->getSize()) {
	        $this->_group = $collection->getFirstItem();
	        $this->setData('group_id', $this->_group->getId());
	        return $this->_group->getId();
	    }
		return false;
	}
	
	
	public function getSlides($group_id = null) {
		if (is_null($group_id)) {
			$group_id = $this->getGroupId();
		}
		if ($group_id && !isset($this->_slides[$group_id]) ){
			$this->_slides[$group_id] = Mage::getModel('slider/slide')
			    ->getCollection()
			    ->addFieldToFilter('group_id', $group_id)
			    ->addFieldToFilter('state', ET_Slider_Model_System_Config_Source_State::STATE_PUBLISHED)
			    ->getItems();
		}
		return $this->_slides[$group_id];
	}
	
	public function getCaptions($slide_id = null) {
	    if (!$slide_id) return array();
		if (!isset($this->_captions[$slide_id])) {
			$this->_captions[$slide_id] = Mage::getModel('slider/caption')
			    ->getCollection()
			    ->addFieldToFilter('slide_id', $slide_id)
			    ->addFieldToFilter('state', ET_Slider_Model_System_Config_Source_State::STATE_PUBLISHED)
			    ->getItems();
		}
		return $this->_captions[$slide_id];
	}
	
}
