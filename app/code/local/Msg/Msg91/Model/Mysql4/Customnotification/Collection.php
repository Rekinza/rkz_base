<?php
    class Msg_Msg91_Model_Mysql4_Customnotification_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
    {
        public function _construct()
        {
            parent::_construct();
            $this->_init('msg91/customnotification');
	    /*$this->_map['fields']['picture_id'] = 'main_table.picture_id';
            $this->_map['fields']['store']   = 'store_table.store_id';*/
        }
        
        /*public function setFirstStoreFlag($flag = false)
        {
            $this->_previewFlag = $flag;
            return $this;
        }*/
      
    }
?>
