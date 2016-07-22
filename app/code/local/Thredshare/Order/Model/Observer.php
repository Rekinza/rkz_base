<?php
class Thredshare_Order_Model_observer{
	public function updateVendorPoints($observer){

		if ($observer->getEvent()->getStatus().""=="really_confirmed"){
			$order = $observer->getEvent()->getOrder();
			$items = $order->getAllItems();
		   

			foreach($items as $i){

			    $status = $i->getStatus();
				if($status != 'Refunded')
			    {
					$vendorId=Mage::getModel("catalog/product")->load($i->getProductId())->getVendor();
				
					if ($vendorId){	
						if($i->getPrice()<750){
						$_point = $i->getPrice()-200;
						Mage::getModel("vendor/info")->addPointsForVendorUserId($i->getPrice()-200,$vendorId);
						}
						else if ($i->getPrice()<5000){
						$_point = 0.7*$i->getPrice();
						Mage::getModel("vendor/info")->addPointsForVendorUserId(0.7*$i->getPrice(),$vendorId);
						}
						else if ($i->getPrice()<50000){
						$_point = 0.8*$i->getPrice();
						Mage::getModel("vendor/info")->addPointsForVendorUserId(0.8*$i->getPrice(),$vendorId);
						}
						else {
						$_point = 0.85*$i->getPrice();
						Mage::getModel("vendor/info")->addPointsForVendorUserId(0.85*$i->getPrice(),$vendorId);
						}
					
						$customer=Mage::getModel('admin/user')->load($vendorId);
							if ($customer && $customer->getEmail()){
								$customerEmail=Mage::getModel("customer/customer")->setWebsiteId(1)->loadByEmail($customer->getEmail());

								if ($customerEmail && $customerEmail->getId()){
				
									$vendorRewardPoint=Mage::getModel('rewardpoints/customer')->load($customerEmail->getId());
									if (!$vendorRewardPoint || !$vendorRewardPoint->getCustomerId()){
					
									$vendorRewardPoint=Mage::getModel('rewardpoints/customer');
									$vendorRewardPoint->setCustomerId($customerEmail->getId());
									}
								}
							}
						
						
						$store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
						
						$oldPoints = $vendorRewardPoint->getMwRewardPoint();
						$newPoints = $oldPoints + $_point;
						
						$results = Mage::helper('rewardpoints/data')->getTransactionExpiredPoints($_point,$store_id);
			    		$expired_day = $results[0];
						$expired_time = $results[1] ;
						$point_remaining = $results[2];

						
						$expired_day = (int)Mage::helper('rewardpoints/data')->getExpirationDaysPoint($store_id);
						$details = "Item Sold".$i->getSKU()."-".$i->getName();
						$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ADMIN_ADDITION,
										           		 'amount'=>(int)$_point, 
										           		 'balance'=>$vendorRewardPoint->getMwRewardPoint(), 
										           		 'transaction_detail'=>$details, 
										           		 'transaction_time'=>now(),
										           		 'expired_day'=>$expired_day,
											    		 'expired_time'=>$expired_time,
											    		 'point_remaining'=>$point_remaining,
					           		                     'history_order_id'=>null,
										           		 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
					    
					    
					    $vendorRewardPoint->saveTransactionHistory($historyData);
			    	}
				}
			}
		}
		else if ($observer->getEvent()->getStatus().""=="undelivered")
		{
			
			$order = $observer->getEvent()->getOrder();
			$order_number = $order->getIncrementId();
			$shipping_address = $order->getShippingAddress();
			$mobile = $shipping_address->getTelephone();
			$name = $shipping_address->getFirstname();

			//starting SMS Sending
			$authKey = "99008A9xcctkyRXr565fed78";

			//Multiple mobiles numbers separated by comma
			$mobileNumber = "91{$mobile}";
			echo $mobileNumber;

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "RKINZA";

			//Your message to send, Add URL encoding here.
			$message = urlencode("Hi {$name}, there seems to be an issue with the delivery of your Rekinza order {$order_number}. Don't miss out on your awesome buy! Call 09810961177 or email hello@rekinza.com to organise a redelivery now.");

			//Define route 
			$route = "4";
			//Prepare you post parameters
			$postData = array(
				'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route
			);

			//API URL
			$url="http://api.msg91.com/sendhttp.php";

			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData
				//,CURLOPT_FOLLOWLOCATION => true
			));


			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


			//get response
			$output = curl_exec($ch);

			//Print error if any
			if(curl_errno($ch))
			{
				echo 'error:' . curl_error($ch);
			}

			curl_close($ch);

			echo $output;

			//ENDING SMS Sending.
		}
		else
		{
				return;
		}
	}
}
?>