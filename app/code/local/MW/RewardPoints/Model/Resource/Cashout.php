<?php
class MW_RewardPoints_Model_Resource_Cashout extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('rewardpoints/cashout', 'id');
    }
}
?>