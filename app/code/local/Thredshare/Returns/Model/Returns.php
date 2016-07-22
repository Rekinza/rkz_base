<?php
class Thredshare_Returns_Model_Returns extends Mage_Core_Model_Abstract{
	
	
	public function _construct(){
	
		$this->_init("thredshare_returns/returns");
	}
	
	public function saveReturn($date,$email_id,$reason,$address1,$address2,$city,$state,$pincode,$mobile,$order_number,$items,$refund_mode,$start_time,$end_time,$acc_holder,$acc_number,$ifsc_code)
	{

			$default_status = "requested";
			
			$this->setReason($reason);
			$this->setEmail($email_id);
			$this->setAddress1($address1);
			$this->setAddress2($address2);
			$this->setCity($city);
			$this->setState($state);
			$this->setPincode($pincode);
			$this->setMobile($mobile);

			$this->setOrderId($order_number);
		
			$date = Mage::getModel('core/date')->date("m/d/Y", $date);
			
			$this->setPickupDate($date);
			$this->setStartTime($start_time);
			$this->setEndTime($end_time);
			
			$this->setItems($items);
			$this->setRefundMode($refund_mode);
			$this->setAccNumber($acc_number);
			$this->setAccHolder($acc_holder);
			$this->setIfscCode($ifsc_code);
			$this->setStatus($default_status);
			
			$this->save();
	
	
	}


}

?>