<?php
class Thredshare_Returns_Model_Resource_Returns_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('thredshare_returns/returns');
    }
}
?>