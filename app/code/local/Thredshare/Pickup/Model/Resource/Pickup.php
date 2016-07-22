<?php
class Thredshare_Pickup_Model_Resource_Pickup extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('thredshare_pickup/pickup', 'id');
    }
}
?>