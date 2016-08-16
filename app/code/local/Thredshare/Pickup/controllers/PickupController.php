<?php
date_default_timezone_set('Asia/Kolkata');
class Thredshare_Pickup_PickUpController extends Mage_Core_Controller_Front_Action{

	
	
	public function requestpickupAction(){
	
		$params=$this->getRequest()->getParams();
		$date=$params['date'];
		$mobile_no=$params['mobile_no'];
		$address1=$params['address1'];
		$address2=$params['address2'];
		$city=$params['city'];
		$state=$params['state'];
		$pincode=$params['pincode'];
		$first_name=$params['first_name'];
		$last_name=$params['last_name'];
		$email=$params['email'];
		$amount=$params['amount'];
		$clothes=$params['clothes'];
		$bags=$params['bags'];
		$shoes=$params['shoes'];
		$accessories=$params['accessories'];
		$item_count=$params['item_count'];
		$start_time=$params['start_time'];
		$end_time=$params['end_time'];
		$unaccepted_action=$params['unaccepted_action'];
		/*$customer=Mage::getSingleton("customer/session")->getCustomer();
		if (!$customer || !$customer->getEmail()){
		$this->_redirect("customer/account/login");
		return;
		}
	   */	
	   
	   /**************Convert clothes,bags,shoes entry into a single string*******************/

			if($clothes)
			{	
				$items = $clothes;
				$items.= ",";
			}
			if($bags)
			{	
				$items .= $bags;
				$items.= ",";
			}
			if($shoes)
			{	
				$items .= $shoes;
				$items.= ",";
			}
			if($accessories)
			{	
				$items .= $accessories;
			}			
			/*****************************************/

	   
	   	Mage::getModel("thredshare_pickup/pickup")->savePickUp($date,$mobile_no,$address1,$address2,$city,$state,$pincode,$first_name,$last_name,$email,$amount,$items,$item_count,$start_time,$end_time,$unaccepted_action);
		Mage::getSingleton('core/session')->addSuccess("Your pick up request has been submitted. A confirmation email and SMS has been sent to you.");

		$newdt = date("d-m-Y", strtotime($date));
			
			$authKey = "99008A9xcctkyRXr565fed78";

			//Multiple mobiles numbers separated by comma	
			$mobileNumber = "91{$mobile_no}";

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "RKINZA";

			//Your message to send, Add URL encoding here.
			$message1 = urlencode("Hi {$first_name}. Your Rekinza pickup is on {$newdt}! Please check www.rekinza.com/sell for our selling guidelines. Thank you for trusting us. Have any questions? Contact us at +91-9810961177/hello@rekinza.com. Team Rekinza");

				//Define route 
				$route = "4";
				//Prepare you post parameters
				$postData1 = array(
				    'authkey' => $authKey,
				    'mobiles' => $mobileNumber,
				    'message' => $message1,
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
				    CURLOPT_POSTFIELDS => $postData1
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

				//echo $output;

		 	$storeId = Mage::app()->getStore()->getStoreId();
            $emailId = "hello@rekinza.com";
            $mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->addBcc('hello@rekinza.com');
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                ->setReplyTo($emailId);
			
			$mailTemplate->sendTransactional( 3,
            array('name'=>"REKINZA","email"=>$emailId),
            $email,
            $first_name,
            array(
            'customer'  =>$first_name,
            'date' => $newdt,
			'day'=>date('l',strtotime($newdt))
            ));
		
			if (!$mailTemplate->getSentSuccess()) {
               	Mage::logException(new Exception('Cannot send pick up mail'));
				var_dump("Cannot send mail");
            }

		//echo Mage::getModel('cms/block')->load('block-brand')->getContent();	
		$this->loadLayout();
		
		/*$block = $this->getLayout()->createBlock(
            'Mage_Core_Block_Template',
            'newpage',
            array('template' => 'thredshare/returns/returns.phtml')
        );
		
		$this->getLayout()->getBlock('content')->append($block);
		$this->getLayout()->getBlock('root')->setTemplate('page/1column.phtml');*/
		$this->getLayout()->getBlock('head')->setTitle('Pickup Request Received');
		$this->renderLayout();
	
	}
	
	
	public function getpickupAction(){
	
		
		$this->loadLayout();
		
		$this->getLayout()->getBlock('head')->setTitle('Rekinza Pickup'); 
		$this->renderLayout();
	
	}
	
	// public function sendpickupreminderAction(){
	
	// 	$time=time()+86400;
	// 	$date=date("Y-m-d",$time);
	// 	$requests=Mage::getModel("thredshare_pickup/pickup")->getCollection()->addFieldToFilter("pick_up_date",array("eq"=>$date));
	// 	$storeId = Mage::app()->getStore()->getStoreId();
	// 	 $emailId = "hello@rekinza.com";
	// 	foreach ($requests as $req){
		
 //           //$customer=Mage::getModel("customer/customer")->load($req->getCustomerId());
	// 	    $first_name = $req->getFirstName();
	// 		$email = $req->getEmail();
 //            $mailTemplate = Mage::getModel('core/email_template');              
 //            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
 //                ->setReplyTo($emailId);
			
	// 		$mailTemplate->sendTransactional( 18,
 //            array('name'=>"REKINZA","email"=>$emailId),
 //            $email,
 //            $first_name,
 //            array(
 //            'customer'  => $first_name,
 //            'date' => $date,
	// 		'day'=>date('l',strtotime($date))
 //            ));
			
	// 		if (!$mailTemplate->getSentSuccess()) {
 //               	Mage::logException(new Exception('Cannot send pick up mail'));
	// 			var_dump("Cannot send mail");
 //            }else{
	// 		//echo "mail sent to ".$customer->getName()." at time ".$date;
	// 		echo "mail sent to ".$email." at time ".$date;
	// 		}
			
	// 	}
	
	
	// }

}

?>