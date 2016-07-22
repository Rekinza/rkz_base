<?php
date_default_timezone_set('Asia/Kolkata');
class Thredshare_Returns_ReturnsController extends Mage_Core_Controller_Front_Action{
	
	public function requestreturnsAction(){

		$params=$this->getRequest()->getParams();
		$date=$params['date'];
		$email_id=$params['email_id'];
		$reason=$params['reason'];
		$address1=$params['address1'];
		$address2=$params['address2'];
		$city=$params['city'];
		$state=$params['state'];
		$pincode=$params['pincode'];
		$mobile=$params['mobile'];		
		$order_number=$params['order_number'];
		
		$itemsarray=$params['p_sku'];
		$items = implode(', ', $itemsarray);
		$refund_mode=$params['refund_mode'];
		$start_time=$params['start_time'];
		$end_time=$params['end_time'];
		$acc_holder=$params['acc_holders_name'];
		$acc_number=$params['acc_number'];
		$ifsc_code=$params['ifsc_code'];
		$item_count = count($itemsarray);

		$p_names = array();
		foreach ($itemsarray as $sku) {
			$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
			array_push($p_names, $product->getName());
		}

		$list_of_items = implode(" ,", $p_names);

		Mage::getModel("thredshare_returns/returns")->saveReturn($date,$email_id,$reason,$address1,$address2,$city,$state,$pincode,$mobile,$order_number,$items,$refund_mode,$start_time,$end_time,$acc_holder,$acc_number,$ifsc_code);

		//get firstname and lastname
		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($email_id);
		$first_name = $customer->getfirstname();
		$last_name = $customer->getlastname(); 


		Mage::getSingleton('core/session')->addSuccess("Your return request is submitted");
			
			/*Get customer name from order ID*/
			
			$order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
			$order->setData(Mage_Sales_Model_Order::STATE_COMPLETE);
			$order->setStatus("return");
			$order->save();

			//If they have no customer id, they're a guest.
			if($order->getCustomerId() == NULL){
				$customer_name = $order->getBillingAddress()->getFirstname();
				
			} else {
				//else, they're a normal registered user.
				$cust = Mage::getModel('customer/customer')->load($order->getCustomerId());
				$customer_name =  $cust->getFirstname();
				
			}
			
			
			$storeId = Mage::app()->getStore()->getStoreId();
            $emailId = "hello@rekinza.com";
            $mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->addBcc('hello@rekinza.com');
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                ->setReplyTo($emailId);
			$mailTemplate->sendTransactional( 26,
            array('name'=>"REKINZA","email"=>$emailId),
            $email_id,
            $customer_name,
			array(
            'customer'  =>$customer_name,
            'date' => $date,
			'items' =>$items,
			'order_number' =>$order_number
            )
            );
		
			if (!$mailTemplate->getSentSuccess()) {
               	Mage::logException(new Exception('Cannot send return mail'));
				var_dump("Cannot send mail");
            }

		$this->loadLayout();
		$this->renderLayout();
	
	}
	
	public function getreturnsAction(){
	
		
		$this->loadLayout();          
 
        $block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'newpage',
            array('template' => 'thredshare/returns/returns.phtml')
        );
 		
        $this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        $this->getLayout()->getBlock('head')->setTitle('Rekinza Returns'); 
		$this->_initLayoutMessages('core/session'); 
        $this->renderLayout();
	
	
	}
	
}

?>