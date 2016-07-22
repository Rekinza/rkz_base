<?php
class Thredshare_Vendor_Model_Resource_Status extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('vendor/status', 'id');
    }
}
?>