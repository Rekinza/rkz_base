<?php

include 'db_config.php';
$path = __DIR__;
include $path.'/../../app/Mage.php';
Mage::app();

 $today = date('Y-m-d');

 $query = "SELECT * from thredshare_pickup where processing_date <= '$today' and status = 'priced'";
 $result = mysql_query($query);
 $count = mysql_numrows($result);

 $live_email_send_error_list = array();

while ($result_set = mysql_fetch_assoc($result)) {
    $pickup_id = $result_set['id'];
    $mobile = $result_set['mobile'];

    //AND query to ensure every accepted item is priced.
    $pickup_query = "SELECT * from inventory where pickup_id = '$pickup_id' and upload_status = 'uploaded' ";
    $pickup_result = mysql_query($pickup_query);
    $pickup_count = mysql_numrows($pickup_result);

    $pickup_total_query = "SELECT * from inventory where pickup_id = '$pickup_id'";
    $pickup_total_result = mysql_query($pickup_total_query);
    $pickup_total_count = mysql_numrows($pickup_total_result);

    $reject_total_query = "SELECT * from inventory where pickup_id = '$pickup_id' and qc_status = 'rejected' ";
    $reject_total_result = mysql_query($reject_total_query);
    $reject_total_count = mysql_numrows($reject_total_result);

    if ($pickup_total_count > 0 && ($pickup_total_count == $reject_total_count) && ($pickup_total_count == $pickup_count)){

        $query_rejects = "UPDATE thredshare_pickup SET live_date = '$today' , status = 'live'  WHERE id = '$pickup_id' ";
        echo $query_rejects.'<br>';
        $result_rejects = mysql_query($query_rejects);
        if ($result_rejects == 'TRUE') {
            //do nothing;
            echo 'Updated rejected only '.$result_set['email'];
        } else {
            $fault = 'id: '.$result_set['id'].' Email: '.$result_set['email'].' table-status: '.$result_set['status']." query failed.\n";
            array_push($live_email_send_error_list, $fault);
        }
    }
    elseif ($pickup_total_count > 0 && ($pickup_total_count == $pickup_count)) {
        $customer_name = $result_set['first_name'];
        $customer_email_id = $result_set['email'];

        $subject = $customer_name.', Your Items Are Live';

        $html = file_get_contents('http://www.rekinza.com/emails/pickup/items-live.html');

        /* Get customer user ID */

        $query_vendor = "SELECT user_id FROM admin_user WHERE email = '$customer_email_id' ";
        $result_vendor = mysql_query($query_vendor);

        $numresult_userid = mysql_numrows($result_vendor);

        if ($numresult_userid > 0) {
            $user_id = mysql_result($result_vendor, 0, 'user_id');

            $q = "SELECT entity_id FROM openwriter_cartmart_profile WHERE user_id = '$user_id' ";
            $result_q = mysql_query($q);

            $numresult_shopid = mysql_numrows($result_q);

            if ($numresult_shopid > 0) {
                $html = str_replace('{customer_name}', $customer_name, $html);

                $shop_id = mysql_result($result_q, 0, 'entity_id');
                    //echo $shop_id;
                $shop_url = "http://www.rekinza.com/cartmart/vendor/profile/id/".$shop_id;
                $account_url = 'http://www.rekinza.com/customer/account/';
                $html = str_replace('http://www.rekinza.com/cartmart/vendor/profile/id/', $shop_url, $html);
                $html = str_replace('account_link', $account_url, $html);

                $body = $html;
            } else {
                echo 'Shop ID not found';
            }
        } else {
            echo 'customer id not found ';
        }
            // above copy-pasted
            //die("here");
        $email = $customer_email_id;
        $flag = sendmail($subject, $body, $email);

        $accepted_total_count = $pickup_total_count - $reject_total_count;
        $acceptance_ratio = $accepted_total_count/$pickup_total_count;

                if ($acceptance_ratio >= 0.75  && $pickup_total_count > 3)
                {
                    $powerpacket = 1;
                }
                else
                {
                    $powerpacket = 0;
                }


        $sms_content = "Hi {$customer_name}. Your closet is now online! Check it out at {$shop_url}. Share the link with your family and friends now! (hello@rekinza.com /+91-9810961177)";

        $flag1 = sendSMS($sms_content, $mobile);

        if ($flag || $flag1) {
            $query1 = "UPDATE thredshare_pickup SET live_date = '$today' , status = 'live'  WHERE id = '$pickup_id' ";
            echo $query1.'<br>';
            $result1 = mysql_query($query1);
            if ($result1 == 'TRUE') {
                echo 'Updated email live send '.$result_set['email'];
                //Add power packet reward to customer Kinza Cash - Stuti
                if($powerpacket == 1)
                {
                    $powerpoints = 100;
                    
                    $customer=Mage::getModel("customer/customer")->setWebsiteId(1)->loadByEmail($customer_email_id);

                    if ($customer && $customer->getId()){

                        $vendorRewardPoint=Mage::getModel('rewardpoints/customer')->load($customer->getId());

                    }
                    
                    $vendorRewardPoint->addRewardPoint($powerpoints);

                    $store_id = Mage::getModel('customer/customer')->load($customer_id)->getStoreId();
                    
                    $oldPoints = $vendorRewardPoint->getMwRewardPoint();
                    $newPoints = $oldPoints + $powerpoints;
                    
                    $results = Mage::helper('rewardpoints/data')->getTransactionExpiredPoints($powerpoints,$store_id);
                    $expired_day = $results[0];
                    $expired_time = $results[1] ;
                    $point_remaining = $results[2];

                    
                    $expired_day = (int)Mage::helper('rewardpoints/data')->getExpirationDaysPoint($store_id);
                    $details = "Power Packet Reward";
                    $historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ADMIN_ADDITION,
                                                     'amount'=>(int)$powerpoints, 
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



            } else {
                $fault = 'id: '.$result_set['id'].' Email: '.$result_set['email'].' table-status: '.$result_set['status']." query failed.\n";
                array_push($live_email_send_error_list, $fault);
            }
        } else {
            $fault = 'id: '.$result_set['id'].' Email: '.$result_set['email'].' table-status: '.$result_set['status']." either email or sms failed no query ran.\n";
            array_push($live_email_send_error_list, $fault);
        }
    } 
    else 
    {
        //all items are not uploaded yet. Do nothing
    }
}

if (!empty($live_email_send_error_list)) {
    $email = 'stuti@rekinza.com';
    $subject = 'CRON JOB ERROR -- live email';
    $fault_string = implode('<br>', $live_email_send_error_list);
    $body = $fault_string;
    sendmail($subject, $body, $email);
}

function sendmail($subject, $body, $email1)
{

    if (!class_exists('PHPMailer')) {
        $path = __DIR__;
        require $path.'/../PHPMailer/class.phpmailer.php';
    }
    $path = __DIR__;
    require_once $path.'/../PHPMailer/class.smtp.php';
    $exc = new phpmailerException();

    try {
        echo 'Preparing email<br>';
        $mail = new PHPMailer(); //New instance, with exceptions enabled
            $mail->IsSMTP();                           // tell the class to use SMTP
            $mail->SMTPAuth = true;                  // enable SMTP authentication
            //$mail->SMTPDebug  = 2;  			
            $mail->Port = 465;                    // set the SMTP server port
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->Username = 'hello@rekinza.com';     // SMTP server username
            $mail->Password = 'r3k!nz@0803';        // SMTP server password
            $mail->SMTPSecure = 'ssl';
            //$mail->AddReplyTo("pratyooshm@floshowers.com","First Last");
            $mail->SetFrom('hello@rekinza.com', 'Rekinza');
        $to = $email;
            //$tos=explode(',',fetchVendorEmailFromName($vendorName));
            //foreach($tos as $to)
            $mail->AddAddress($to);
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
}


function sendSMS($sms_content, $mobile){
                //starting SMS
                $authKey = "99008A9xcctkyRXr565fed78";

                //Multiple mobiles numbers separated by comma
                $mobileNumber = "91{$mobile}";
                echo $mobileNumber;

                //Sender ID,While using route4 sender id should be 6 characters long.
                $senderId = "RKINZA";

                //Your message to send, Add URL encoding here.
                $message = urlencode($sms_content);

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
                    return 0;
                }

                curl_close($ch);

                echo $output;
                return 1;

                //ENDING SMS
}
