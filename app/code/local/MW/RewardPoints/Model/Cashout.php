<?php
date_default_timezone_set('Asia/Kolkata');
class MW_RewardPoints_Model_Cashout extends Mage_Core_Model_Abstract{
	
	protected function _construct(){
        $this->_init('rewardpoints/cashout');
    }
	
	public function cashOutRequest($type,$name,$account,$bankName,$branchName,$ifsc,$account_type,$amount){
		
		
		if (Mage::getSingleton('customer/session')->isLoggedIn()){
			
		$this->setType($type);
		$this->setName($name);
		$this->setAccount($account);
		$this->setBankName($bankName);
		$this->setBranchName($branchName);
		$this->setIfsc($ifsc);
		$this->setAccountType($account_type);
		$this->setPoints($amount);
		$this->setMessage('');
		$this->setTimestamp(time());
		$this->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
		$this->save();
		}
	
	}
	
}

?>