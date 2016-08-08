<?php
date_default_timezone_set('Asia/Kolkata');
class Thredshare_Returns_ReturnsController extends Mage_Core_Controller_Front_Action
{

    public function requestreturnsAction()
    {
        function sendmail($subject, $body, $email, $cc_email)
        {
            if (!class_exists('PHPMailer')) {
                // $path = __DIR__;
                require '/backend/PHPMailer/class.phpmailer.php';
            }
            //$path = __DIR__;
            require_once '/backend/PHPMailer/class.smtp.php';
            $exc = new phpmailerException();
            try {
                echo 'Preparing email<br>';
                $mail = new PHPMailer(); //New instance, with exceptions enabled
                $mail->IsSMTP();                           // tell the class to use SMTP
                $mail->SMTPAuth = true;                  // enable SMTP authentication
                //$mail->SMTPDebug  = 2;
                $mail->Port = 465;                    // set the SMTP server port
                $mail->Host = 'smtp.gmail.com'; // SMTP server
                //change here
                $mail->Username = 'hello@rekinza.com';     // SMTP server username
                $mail->Password = 'r3k!nz@0803';        // SMTP server password
                $mail->SMTPSecure = 'ssl';
                //$mail->AddReplyTo("pratyooshm@floshowers.com","First Last");
                $mail->SetFrom('hello@rekinza.com', 'Rekinza');
                $to = $email;
                //$tos=explode(',',fetchVendorEmailFromName($vendorName));
                //foreach($tos as $to)
                $mail->AddAddress($to);
                $mail->AddCC($cc_email);
                $mail->Subject = $subject;
                //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
                $mail->WordWrap = 50; // set word wrap
                $mail->Body = $body;
                $mail->IsHTML(true); // send as HTML
                if ($mail->Send() == true) {
                    echo 'Message has been sent.';
                    return 1;
                } else {
                    echo 'Mailer Error: '.$mail->ErrorInfo;
                    return 0;
                }
            } catch (phpmailerException $e) {
                echo $e->errorMessage();
                echo 'Oh no';
                return 0;
            }
        } //sendmail ends

        //commom code begins

        include 'db_config.php';
		$params=$this->getRequest()->getParams();
        $order_number = $params['order_number'];
		$email_id=$params['email_id'];
        $itemsarray = $params['p_sku'];
        //get firstname and lastname
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email_id);
        $first_name = $customer->getfirstname();
        $last_name = $customer->getlastname();
        /*Get customer name from order ID*/
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
        //If they have no customer id, they're a guest.
        if ($order->getCustomerId() == null) 
        {
            $customer_name = $order->getBillingAddress()->getFirstname();
        } 
        else 
        {
            //else, they're a normal registered user.
            $cust = Mage::getModel('customer/customer')->load($order->getCustomerId());
            $customer_name = $cust->getFirstname();
        }

        $query_check = "SELECT id, items, status, pickup_date, logistics_partner, waybill_number from thredshare_returns where order_id = '$order_number' and email = '$email_id' ORDER BY id DESC LIMIT 1";
        $result_check = mysql_query($query_check);
        $num_rows = mysql_numrows($result_check);
        $today = date('Y-m-d');
        $return_pickup_date = mysql_result($result_check,0,'pickup_date');
        $status = mysql_result($result_check,0,'status');


        //case 1
        if ($num_rows == 1 && ($today < $return_pickup_date)) 
        {
            $return_id = mysql_result($result_check,0,'id');
            $old_items = mysql_result($result_check,0,'items');
            $old_items_array = explode(', ', $old_items);
            foreach ($old_items_array as $old_item) {
                array_push($itemsarray, $old_item);    //add old skus to the new array of skus
            }
            $items = implode(', ', $itemsarray);
            try{ 
		        //send updated email to customer first
		        $storeId = Mage::app()->getStore()->getStoreId();
		        $emailId = "hello@rekinza.com";
		        $mailTemplate = Mage::getModel('core/email_template');
		        $mailTemplate->addBcc('hello@rekinza.com');
		        $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
		            ->setReplyTo($emailId);
		        $mailTemplate->sendTransactional(26,array('name' => 'REKINZA', 'email' => $emailId),
		        $email_id,
		        $customer_name,
		        array(
		        'customer' => $customer_name,
		        'date' => $return_pickup_date,
		        'items' => $items,
		        'order_number' => $order_number,
		        )
		        );
			}
        	catch(Exception $e){
                Mage::logException(new Exception('Cannot send return mail'));
                die("There's been a mistake, Please email us or try making the return request again.");
           //email ends
			}
            if ($status == 'scheduled' && ($today < $return_pickup_date)) {
                //email to logistic
                $logistics_partner = mysql_result($result_check,0,'logistics_partner');
                if($logistics_partner == "NuvoEx"){
                    $email = "ops@nuvoex.com";
                }
                elseif($logistics_partner == "Pickrr"){
                    $email = "info@pickrr.com";
                }
                elseif($logistics_partner == "Pyck"){
                    $email = "help@pyck.in";
                }
                foreach ($itemsarray as $i) {
                    $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $i);
                    $items_name .= $_product->getName();
                    $items_name .= ', ';  //getting names of each sku product
                }
                $item_count = count($itemsarray);
                
                $waybill = mysql_result($result_check,0,'waybill_number');
                $email = '';	//add nuvoex or pyck email
                $cc_email = 'stuti@rekinza.com';
                $subject = "$logistics_partner : $waybill";
                $fault_string = "Please update the number of items for $waybill. $item_count items need to be picked up and their description is $items_name";
                $body = $fault_string;
                $sentmail = sendmail($subject, $body, $email, $cc_email);
                if (!$sentmail) {
                    $email_err = 'komal@rekinza.com';
                    $cc_err = 'stuti@rekinza.com';
                    $subject_err = 'RETURN FORM -- returns scheduling problem';
                    $fault_string_err = "Mailer $mail->ErrorInfo for waybill $waybill";
                    $body_err = $fault_string_err." $subject and $body and please update the thredshare_returns for id: $return_id table with this $items";
                    sendmail($subject_err, $body_err, $email_err, $cc_err);
                }
            }
            //dont need separate for "requested" status
            try {
                $queryran = Mage::getModel('thredshare_returns/returns')->updateReturn($return_id, $items);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('There has been an Error'.$e->getMessage());
                $email_err = 'komal@rekinza.com';
                $cc_err = 'stuti@rekinza.com';
                $subject_err = 'RETURN FORM -- query update failed';
                $fault_string_err = "update query failed for $return_id and $items";
                $body_err = $fault_string_err;
                sendmail($subject_err, $body_err, $email_err, $cc_err);
            }
        } 

        //case 2
        elseif ($num_rows == 0 || ($num_rows == 1 && ($return_pickup_date >= $today))) 
        {

		$date=$params['date'];
		$reason=$params['reason'];
		$address1=$params['address1'];
		$address2=$params['address2'];
		$city=$params['city'];
		$state=$params['state'];
		$pincode=$params['pincode'];
		$mobile=$params['mobile'];
		
		$items = implode(', ', $itemsarray);
		$refund_mode=$params['refund_mode'];
		$start_time=$params['start_time'];
		$end_time=$params['end_time'];
		$acc_holder=$params['acc_holders_name'];
		$acc_number=$params['acc_number'];
		$ifsc_code=$params['ifsc_code'];
		$item_count = count($itemsarray);
		
		Mage::getModel('thredshare_returns/returns')->saveReturn($date,$email_id,$reason,$address1,$address2,$city,$state,$pincode,$mobile,$order_number,$items,$refund_mode,$start_time,$end_time,$acc_holder,$acc_number,$ifsc_code);

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
        } 

        //case 3
        else {
            die("There's been a mistake, Please email us");
        }

			
		$order->setData(Mage_Sales_Model_Order::STATE_COMPLETE);
		$order->setStatus("return");
		$order->save();
		Mage::getSingleton('core/session')->addSuccess("Your return request is submitted");
		
			

		$this->loadLayout();
		$this->renderLayout();
	
	}
	
	public function getreturnsAction()
	{
	
		
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