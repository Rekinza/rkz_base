<?php
class Thredshare_Brands_Model_Resource_Brands_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('thredshare_brands/brands');
    }
}
?>