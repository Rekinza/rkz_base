<?php
class Thredshare_Pickup_Model_Pickup extends Mage_Core_Model_Abstract{
	
	
	public function _construct(){
	
		$this->_init("thredshare_pickup/pickup");
	}
	
	public function savePickUp($date,$mobile,$address1,$address2,$city,$state,$pincode,$first_name,$last_name,$email,$amount,$items,$item_count,$start_time,$end_time,$unaccepted_action){
	
		//if (Mage::getSingleton('customer/session')->isLoggedIn()){    No need to check if customer is logged in. Email is now being sent as a mandatory field
		
		//	$customer=Mage::getSingleton('customer/session')->getCustomer();    Email is now being sent as a mandatory field

			
			$this->setCustomerId('0');               // Hard coding to 0 as it is not mandatory to have an account to sign up as a seller
			$this->setEmail($email);
			$this->setState($state);
			$this->setMobile($mobile);
			$this->setAddress1($address1);
			$this->setAmount($amount);
			$this->setItems($items);
			$this->setAddress2($address2);
			$this->setCity($city);
		
			$date = Mage::getModel('core/date')->date("m/d/Y", $date);
			
			$this->setPickUpDate($date);
			$this->setStartTime($start_time);
			$this->setEndTime($end_time);
			
			$this->setItemCount($item_count);
			$this->setPickupCost('0');
			$this->setPincode($pincode);
			$this->setFirstName($first_name);
			$this->setLastName($last_name);
			$this->setUnacceptedAction($unaccepted_action);
			
			$this->save();
		//}
	
	
	}


}

?>