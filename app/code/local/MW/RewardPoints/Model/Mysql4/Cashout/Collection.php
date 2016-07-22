<?php
class MW_RewardPoints_Model_Mysql4_Cashout_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('rewardpoints/cashout');
    }
}
?>