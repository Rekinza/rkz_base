<?php

class MW_RewardPoints_Model_Customer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rewardpoints/customer');
    }
	
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * 
     * @param $data=array('type_of_transaction'=>y, 'amount'=>z, 'transaction_detail'=>a, 'transaction_time'=>b)
     */
	
    public function saveTransactionHistory($data = array())
    {
    	$data['customer_id'] = $this->getId();
    	$history = Mage::getModel('rewardpoints/rewardpointshistory');
    	$history->setData($data);
    	$history->save();
    }
    
	public function getRewardPoint()
	{
		return $this->getMwRewardPoint();
	}
	
	public function addRewardPoint($point)
	{
		$point = (int)$point;
		$this->setMwRewardPoint($this->getMwRewardPoint()+ $point);
		$this->save();
	}
	
	
	
	public function getFriend()
	{
		if($this->getMwFriendId())
			return Mage::getModel('rewardpoints/customer')->load($this->getMwFriendId());
		return false;
	}
	
	public function getCustomerModel()
	{
		return Mage::getModel('customer/customer')->load($this->getId());
	}
	
	public function customerSaveAfterLogin($param)
	{
		$customer = Mage::getModel('customer/customer')->load($param->getModel()->getId());
		$customer_id = $customer ->getId();
		$store_id = $customer->getStoreId();
		if(Mage::helper('rewardpoints')->moduleEnabled())
		{
            //Check invition information if exist add reward point to friend
        	$friend_id = Mage::getModel('core/cookie')->get('friend');
        	$customer_group_id = Mage::getModel('customer/customer')->load($customer_id)->getGroupId();
            if($friend_id)
            {
	            Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($friend_id, 0);	
	            $friend = Mage::getModel('rewardpoints/customer')->load($friend_id);
	            //$point = Mage::getStoreConfig('rewardpoints/config/reward_point_for_friend_registering');
	            $type_of_transaction = MW_RewardPoints_Model_Type::FRIEND_REGISTERING;
	           // $point = (double)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
				$results = Mage::getModel('rewardpoints/activerules')->getResultActiveRulesExpiredPoints($type_of_transaction,$customer_group_id,$store_id);
				$point = $results[0];
			 	$expired_day = $results[1];
				$expired_time = $results[2];
			 	$point_remaining = $results[3];
	            $size_history = Mage::helper('rewardpoints/data')->sizeofTransactionHistory($friend_id,$type_of_transaction,$customer_id);
	            if($friend->getId() && $point && $size_history == 0 )
	            {
		            $friend->setMwRewardPoint($friend->getMwRewardPoint() + $point);
		            $friend->save();
		            $historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_REGISTERING,
							             'amount'=>$point, 
							             'balance'=>$friend->getMwRewardPoint(), 
							             'transaction_detail'=>$customer_id, 
							             'transaction_time'=>now(),
							             'expired_day'=>$expired_day,
							    		 'expired_time'=>$expired_time,
							    		 'point_remaining'=>$point_remaining,
							             'status'=>MW_RewardPoints_Model_Status::COMPLETE);
		            $friend->saveTransactionHistory($historyData);
		            
		            // send mail when points changed
		            $store_id = Mage::getModel('customer/customer')->load($friend->getId())->getStoreId();
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($friend->getId(),$historyData, $store_id);
	            }
            }
		}
	}
	
	public function customerSaveAfterRegister($param)
	{
		$customer = $param->getCustomer();
		$customer_id = $customer ->getId();
		$store_id = $customer->getStoreId();
		if($customer_id && Mage::helper('rewardpoints')->moduleEnabled())
		{
            //Check invition information if exist add reward point to friend
        	$friend_id = Mage::getModel('core/cookie')->get('friend');
        	$customer_group_id = Mage::getModel('customer/customer')->load($customer_id)->getGroupId();
            if($friend_id)
            {
	            Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($friend_id, 0);	
	            $friend = Mage::getModel('rewardpoints/customer')->load($friend_id);
	            //$point = Mage::getStoreConfig('rewardpoints/config/reward_point_for_friend_registering');
	            $type_of_transaction = MW_RewardPoints_Model_Type::FRIEND_REGISTERING;
	           // $point = (double)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
				$results = Mage::getModel('rewardpoints/activerules')->getResultActiveRulesExpiredPoints($type_of_transaction,$customer_group_id,$store_id);

				$point = $results[0];
			 	$expired_day = $results[1];
				$expired_time = $results[2];
			 	$point_remaining = $results[3];
	            $size_history = Mage::helper('rewardpoints/data')->sizeofTransactionHistory($friend_id,$type_of_transaction,$customer_id);
	            if($friend->getId() && $point && $size_history == 0 && Mage::helper('rewardpoints')->checkCustomerMaxBalance($friend->getId(),$store_id,$point) )
	            {
		            $friend->setMwRewardPoint($friend->getMwRewardPoint() + $point);
		            $friend->save();
		            $historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::FRIEND_REGISTERING,
							             'amount'=>$point, 
							             'balance'=>$friend->getMwRewardPoint(), 
							             'transaction_detail'=>$customer_id, 
							             'transaction_time'=>now(),
							             'expired_day'=>$expired_day,
							    		 'expired_time'=>$expired_time,
							    		 'point_remaining'=>$point_remaining,
							             'status'=>MW_RewardPoints_Model_Status::COMPLETE);
		            $friend->saveTransactionHistory($historyData);
		            
		            // send mail when points changed
		            $store_id = Mage::getModel('customer/customer')->load($friend->getId())->getStoreId();
					Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($friend->getId(),$historyData, $store_id);
	            }
            }
			
			//init reward points of customer
            //$point = Mage::getStoreConfig('rewardpoints/config/reward_point_for_registering');
            $type_of_transaction = MW_RewardPoints_Model_Type::REGISTERING;
	        //$point = (double)Mage::getModel('rewardpoints/activerules')->getPointActiveRules($type_of_transaction,$customer_group_id,$store_id);
            $results = Mage::getModel('rewardpoints/activerules')->getResultActiveRulesExpiredPoints($type_of_transaction,$customer_group_id,$store_id);
			$point = $results[0];
		 	$expired_day = $results[1];
			$expired_time = $results[2];
		 	$point_remaining = $results[3];
            Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer_id, $friend_id);	
            $size_history = Mage::helper('rewardpoints/data')->sizeofTransactionHistory($customer_id,$type_of_transaction);
            
            // add point when customer Subcriber
            $this->updatePointSubcriber($customer_id);
			//Save history transaction 
			if($point && $size_history == 0 && Mage::helper('rewardpoints')->checkCustomerMaxBalance($customer_id,$store_id,$point)){
				$_customerModel = Mage::getModel('rewardpoints/customer')->load($customer_id);
				$_customerModel->setMwRewardPoint($_customerModel->getMwRewardPoint() + $point);
				$_customerModel->save();
				$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::REGISTERING, 
									 'amount'=>$point, 
									 'balance'=>$_customerModel->getMwRewardPoint(), 
									 'transaction_detail'=>'',
									 'transaction_time'=>now(),
									 'expired_day'=>$expired_day,
						    		 'expired_time'=>$expired_time,
						    		 'point_remaining'=>$point_remaining,
									 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
				$_customerModel->saveTransactionHistory($historyData);
				
				// send mail when points changed
	            $store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
				Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($customer_id,$historyData, $store_id);
					
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('rewardpoints')->__('You recived %s %s points for signing up.',$point, Mage::helper('rewardpoints')->getPointCurency($store_id)));
			}
			//Mage::dispatchEvent('customer_account_registed_rewardpoint');
		}
	}
	public function updatePointSubcriber($customer_id)
    {
		$collection = Mage::getResourceSingleton('newsletter/subscriber_collection');
		$collection->useOnlyCustomers()->useOnlySubscribed()->addFieldToFilter('customer_id',$customer_id);
		$customer_id = (int)$collection->getFirstItem()->getCustomerId();
    	if($customer_id){
    		$customer_group_id = Mage::getModel('customer/customer')->load($customer_id)->getGroupId();
    		$store_id = Mage::app()->getStore()->getId();
    		$type_of_transaction = MW_RewardPoints_Model_Type::SIGNING_UP_NEWLETTER;
	       
			$results = Mage::getModel('rewardpoints/activerules')->getResultActiveRulesExpiredPoints($type_of_transaction,$customer_group_id,$store_id);
			$rewardpoints = $results[0];
		 	$expired_day = $results[1];
			$expired_time = $results[2];
		 	$point_remaining = $results[3];
    		Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer_id, 0);	
    		$_customer = Mage::getModel('rewardpoints/customer')->load($customer_id);
			$size_history = Mage::helper('rewardpoints/data')->sizeofTransactionHistory($customer_id,$type_of_transaction);
			if($rewardpoints && $size_history == 0){
				$_customer->addRewardPoint($rewardpoints);
				$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SIGNING_UP_NEWLETTER, 
									 'amount'=>(int)$rewardpoints, 
									 'balance'=>$_customer->getMwRewardPoint(), 
									 'transaction_detail'=>'', 
									 'transaction_time'=>now(), 
									 'expired_day'=>$expired_day,
						    		 'expired_time'=>$expired_time,
						    		 'point_remaining'=>$point_remaining,
									 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
				$_customer->saveTransactionHistory($historyData);
				
				// send mail when points changed
				Mage::helper('rewardpoints')->sendEmailCustomerPointChanged($_customer->getId(),$historyData, $store_id);
			}

    	}
    }
	
	public function fetchCash($type,$name,$account,$bankName,$branchName,$ifsc,$account_type,$amount){
	//fetchCash($type,$message,$amount){
	
		if ($amount && $type && $name){
			if (Mage::getSingleton('customer/session')->isLoggedIn()){
			$customerId=Mage::getSingleton("customer/session")->getCustomer()->getId();
				if ($customerId){
				
					$rewCust=$this->load($customerId);
					if ($rewCust && $rewCust->getCustomerId()){
						
						if ($this->getMwRewardPoint()>=$amount){
							
							$this->setMwRewardPoint($this->getMwRewardPoint()-$amount);
							$this->save();

							//Added to display cashout requests in Magento admin for customers - Stuti
							$details = "Cashout Requested for ".$amount;
							$store_id = Mage::getSingleton("customer/session")->getCustomer()->getStoreId();

							$results = Mage::helper('rewardpoints/data')->getTransactionExpiredPoints($_point,$store_id);
    						$expired_day = $results[0];
							$expired_time = $results[1] ;
							$point_remaining = $results[2];						


							$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ADMIN_SUBTRACT,
									 'amount'=>$amount, 
									 'balance'=>$this->getMwRewardPoint(), 
									 'transaction_detail'=>$details,
									 'transaction_time'=>now(),
									 'expired_day'=>$expired_day,
								     'expired_time'=>$expired_time,
								     'point_remaining'=>$point_remaining,
									 'status'=>MW_RewardPoints_Model_Status::COMPLETE);

							$this->saveTransactionHistory($historyData);

							//Email trigger for successful cashout

							$customer = Mage::getSingleton("customer/session")->getCustomer();
	            			$emailId = "hello@rekinza.com";
				            $mailTemplate = Mage::getModel('core/email_template');
							$mailTemplate->addBcc('hello@rekinza.com');
				            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$store_id))
				                ->setReplyTo($emailId);
							$mailTemplate->sendTransactional( 32,
				            array('name'=>"REKINZA","email"=>$emailId),
				            $customer->getEmail(),
				            strtoupper($customer->getName()),
				            array(
				            'customer'  =>strtoupper($customer->getName()),
				            'date' => $date,
							'day'=>date('l',strtotime($date))
				            ));
							
						
							if (!$mailTemplate->getSentSuccess()) {
				               	Mage::logException(new Exception('Cannot send cashout mail'));
								var_dump("Cannot send mail");
				            }

							Mage::getModel("rewardpoints/cashout")->cashOutRequest($type,$name,$account,$bankName,$branchName,$ifsc,$account_type,$amount);
							Mage::getSingleton("core/session")->addSuccess("Congrats your $amount Kinzacash have been cashed out. Please check your email for details.");
							
						}
						else
						{
						
							Mage::getSingleton("core/session")->addError("You do not have sufficient points to cash out.Please try again.");
						}
						
					}
					
				}
			
			}
		
		}
	
	}
    
    
}