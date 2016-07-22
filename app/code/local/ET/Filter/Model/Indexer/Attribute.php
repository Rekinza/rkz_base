<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Model_Indexer_Attribute extends Mage_Index_Model_Indexer_Abstract{

    protected $_matchedEntities = array(
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        ),
    );

    protected function _construct(){
        $this->_init('filter/indexer_attribute');
    }

    protected function _processEvent(Mage_Index_Model_Event $event){
        $this->callEventHandler($event);
    }

    protected function _registerEvent(Mage_Index_Model_Event $event){
        return $this;
    }

    public function getName(){
        return Mage::helper('filter')->__('ET Filter');
    }

    public function getDescription(){
        return Mage::helper('filter')->__('Index attribute options for layered navigation filters');
    }

}