<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_Position extends Mage_Core_Model_Abstract {
    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init('megamenu/position');
    }

    /**
     * If object is new adds creation date
     *
     * @return ET_Megamenu_Model_Position
     */
    protected function _beforeSave() {
        parent::_beforeSave();
        if ($this->isObjectNew('position')) {
            $this->setData('created_at', Varien_Date::now());
        }
        return $this;
    }
    
    protected function _afterSave(){
    	parent::_afterSave();
    	
    	if ( ($root = $this->_getRoot($this->getId())) === false ){
    		$this->_createRoot();
    	} else {
    		$root->setTitle( $this->getTitle() . ' - Root' )
				->setParentId(0)
				->setIsRoot(1)
				->save();
    	}
    }
    
    protected function _getRoot(){
    	$collection = Mage::getModel('megamenu/menu')->getCollection();
    	$collection->addFieldToFilter('position_id', $this->getId());
    	$collection->addFieldToFilter('lft', 0);
    	if ( $collection->getSize() ){
    		return $collection->getFirstItem();
    	}
    	return false;
    }
    
    protected function _createRoot(){
    	return Mage::getModel('megamenu/menu')
    	->setData( array(
    			'position_id' => $this->getId(),
    			'title' => $this->getTitle() . ' - Root',
    			'lft'=>0,
    			'rgt'=>1,
    			'parent_id'=>0,
    			'is_root'=>0,
    			'depth'=>0
    	) )
    	->save();
    }
    
    public function getRootId(){
    	$collection = Mage::getModel('megamenu/menu')
    		->getCollection()
    		->addFieldToFilter('position_id', $this->getId())
    		->addFieldToFilter('lft', 0);
    	if ( $collection->getSize() ){
    		return $collection->getFirstItem()->getid();
    	}
    	return 0;
    }
}