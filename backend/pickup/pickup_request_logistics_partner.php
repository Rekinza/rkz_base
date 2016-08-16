<?php
$path = __DIR__;
include $path.'/../../app/Mage.php';
Mage::app();
include 'db_config.php';

$today = date("Y-m-d");

$today = date('Y-m-d H:i:s', strtotime($today));

$tomorrow = strtotime ( '+24 hours' , strtotime ($today )) ;  //Find tomorrow's date;

$tomorrow = date('Y-m-d', $tomorrow);

$query = "SELECT id FROM thredshare_pickup WHERE pick_up_date = '$tomorrow' AND waybill_number = '' AND status = 'requested'";

$result = mysql_query($query);

$numresult = mysql_numrows($result);


$pickup_schedule_error_list = array();

if($numresult >0)
{
	$i = 0;
	while ( $i < $numresult )
	{
		$pickup_id = mysql_result($result,$i,'id');
		
		$query_fetch_pickup_details = "SELECT * FROM thredshare_pickup WHERE id = '$pickup_id'";
		
		$result_pickup_details = mysql_query($query_fetch_pickup_details);
		
		
		if (mysql_numrows($result_pickup_details) != 0)
		{
					
			$mobile_no = mysql_result($result_pickup_details,0,'mobile');
			$address1 = mysql_result($result_pickup_details,0,'address1');
			$address2 = mysql_result($result_pickup_details,0,'address2');
			$city = mysql_result($result_pickup_details,0,'city');
			$state = mysql_result($result_pickup_details,0,'state');
			$pincode = mysql_result($result_pickup_details,0,'pincode');
			$email_id = mysql_result($result_pickup_details,0,'email');
			$first_name = mysql_result($result_pickup_details,0,'first_name');
			$last_name = mysql_result($result_pickup_details,0,'last_name');
			$amount = mysql_result($result_pickup_details,0,'amount');
			$items = mysql_result($result_pickup_details,0,'items');
			$item_count = mysql_result($result_pickup_details,0,'item_count');

			//Email for pickup reminder
			$storeId = Mage::app()->getStore()->getStoreId();
            $emailId = "hello@rekinza.com";
            $mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->addBcc('hello@rekinza.com');
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$storeId))
                ->setReplyTo($emailId);
			
			$mailTemplate->sendTransactional( 18,
            array('name'=>"REKINZA","email"=>$emailId),
            $email_id,
            $first_name,
            array(
            'customer'  =>$first_name,
            'date' => $tomorrow,
			'day'=>date('l',strtotime($tomorrow))
            ));
			
			//SMS for pickup reminder
			$authKey = "99008A9xcctkyRXr565fed78";

			//Multiple mobiles numbers separated by comma	
			$mobileNumber = "91{$mobile_no}";

			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "RKINZA";

			//Your message to send, Add URL encoding here.
			$message1 = urlencode("Hi {$first_name}. Your Rekinza pickup is tomorrow! Our logistics partner will get in touch with you to coordinate the pickup. It takes 4-10 days for your items to reach us. Tracking details will be emailed after pickup. Thank you for trusting us. Have any questions? Contact us at +91-9810961177/hello@rekinza.com. Team Rekinza");

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

				echo $output;
			
			$response_nuvoex = request_nuvoex_for_reverse_pickup($pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);
			
			if($response_nuvoex['request_status'] == 'FALSE')  // If unable to schedule with NuvoEx
			{	
				
				//Save the error msg in the waybill number column
				
				$error_msg = $response_nuvoex['error'];
				
				$query_update_waybill_error = "UPDATE thredshare_pickup SET waybill_number = '$response' WHERE id = '$pickup_id'";
		
				$result_update_waybill_error = mysql_query($query_update_waybill_error);
				
				//Request Pickrr for reverse pick up since it has failed with NuvoEx
				
				$response_pickrr = request_pickrr_for_reverse_pickup($pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);
				

				if($response_pickrr['request_status'] == 'TRUE')
				{
					$awb = $response_pickrr['awb'];
					
					$query_update_pickup_status = "UPDATE thredshare_pickup SET status = 'scheduled', logistics_partner = 'Pickrr', waybill_number = '$awb' WHERE id = '$pickup_id'";
				
					$result_update_pickup_status = mysql_query($query_update_pickup_status);			
				}
				else
				{
					$error_msg = $response_pickrr['error'];
				
					$query_update_waybill_error = "UPDATE thredshare_pickup SET waybill_number = '$error_msg' WHERE id = '$pickup_id'";
		
					$result_update_waybill_error = mysql_query($query_update_waybill_error);
					
					//Add to error list
					$fault = "email id: ".$email_id." Unable to schedule with any partner\n";
					array_push($pickup_schedule_error_list, $fault);
					
				}
			}
			else
			{
				$awb = $response_nuvoex['awb'];
				$query_update_pickup_status = "UPDATE thredshare_pickup SET status = 'scheduled', logistics_partner = 'NuvoEx', waybill_number = '$awb' WHERE id = '$pickup_id'";
				
				$result_update_pickup_status = mysql_query($query_update_pickup_status);				
			}
			
		}
		else
		{
			//Add to error list
			$fault = "pick up id: ".$pickup_id." Unable to fetch details. Not able to schedule\n";
			array_push($pickup_schedule_error_list, $fault);
			//echo "Unable to fetch details of pick up ID".$pickup_id."<br>";
		}
	$i++;
	}
	
	if (!empty($pickup_schedule_error_list))
	{
		$email = "komal@rekinza.com";
		$cc = "stuti@rekinza.com";
		$subject = "CRON JOB ERRORS -- pickup scheduling";
		$fault_string = implode("<br>",$pickup_schedule_error_list);
		$body = $fault_string;
		sendmail($subject , $body, $email, $cc);
	}
	

}

else
{
	echo "No data to process";
}

function request_nuvoex_for_reverse_pickup($pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name,$items, $item_count)
{
	
	// Currently some pincodes are not operational for NuvoEX
	
	$non_serviceable_pincodes = array(410210,400612,400601,400602,400603,400604,400605,400606,400607,400608,400609,400610,400614,400615,400701,400703,400705,400706,400708,400709,400710,401105,401101,401104,401107,410206,410209,700006,700017,700014,700054,700019,700028,700034,700007,700053,700032,700040,700023,700027,700029,700016,700026,700020,700051,700030,700107,700008,700033,700052,700060,700071,700025,700002,700046,700031,700099,700012,700024,700094,700010,700067,700068,700037,700005,700039,700042,700087,700072,700073,700069,700001,700003,700004,700045,700084,700086,700047,700041,700013,700015,700011,700091,700092,700064,700103,700055,700089,700048,700059,700021,700022,700101,700043,700088,700018,700009,700061,700062,700063,700065,700066,700074,700075,700078,700082,700085,700090,700095,700096,700097,700098,700100,700102,700105,700106,700147,700148,700149,700150,700154,700156,700049,700050,700104,700038,700070,700152,700159,700093,700079,700077,700153,700146,700136);
		
	if (in_array($pincode,$non_serviceable_pincodes))
	{
		$return_array['error'] = 'non-servicable pincode';
		$return_array['awb'] = '';
		$return_array['request_status'] = 'FALSE';
		
		echo  "Thane - Failed with Nuvo Ex ID ".$pickup_id;
		
		return $return_array;
		
	}
	
	
	$awb = get_and_reserve_first_available_nuvoex_waybill();  //Get and reserve first available waybill number
				
	$query_set_waybill_number_for_pickup_id = "UPDATE thredshare_pickup SET waybill_number = '$awb' WHERE id = '$pickup_id'";  //Reserve waybill number for this pick up ID
		
	$result_set_waybill_number_for_pickup_id = mysql_query($query_set_waybill_number_for_pickup_id);
	
	$response = send_pickup_request_to_nuvoex($awb, $pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);
			
	if($response != 200)  // If some error occured in logistics partner end
	{
		$response = substr($response,0,9);
		
		$response = "Nuvo ".$response;
		
		//Release the waybill number that was reserved for this pick up ID
		$query_unset_waybill_number_usage = "UPDATE nuvoex_waybills SET used_flag = 0 WHERE waybill = '$awb'";
		$result_unset_waybill_number_usage = mysql_query($query_unset_waybill_number_usage);
		
		$return_array['error'] = $response;
		$return_array['request_status'] = 'FALSE';
		
		
	}
	else
	{
		$return_array['error'] = '';
		$return_array['awb'] = $awb;
		$return_array['request_status'] = 'TRUE';
			
	}
	
	return $return_array;
	
}

function get_and_reserve_first_available_nuvoex_waybill()
		{
			$type = 0;
			$query = 'SELECT waybill FROM nuvoex_waybills WHERE used_flag = 0 LIMIT 1';

			$result = mysql_query($query);

			$numresult = mysql_numrows($result);

			if( $numresult >0 )
			{
				$awb = mysql_result($result,0,'waybill');
			}
			$query = "UPDATE nuvoex_waybills SET used_flag = 1 WHERE waybill = '$awb'";
			$result = mysql_query($query);
			
			$awb = trim($awb);
			
			return $awb;
		}
					
function send_pickup_request_to_nuvoex($awb, $pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name,$items, $item_count)
{
	$url = "http://rekinza:123456@api.nuvoex.com/api/manifest/upload";

	$pkgvalue = 1000 * $item_count;
	$weight = 250 * $item_count;
		
	$fields1 = array(
							"AWB" => $awb,
							"CLIENT ORDER NO" => $pickup_id,
							"CUST NAME" => $first_name." ".$last_name,
							"CUST ADDRESS LINE 1" => $address1,
							"CUST ADDRESS LINE 2" => $address2,
							"CUST CITY" => $city,
							"Pincode" => $pincode,
							"PHONE 1" => $mobile_no,
							"PHONE 2" => "",
							"Weight" => $weight,
							"PACKAGE DESCRIPTION" => $items,
							"PACKAGE VALUE" => $pkgvalue,
							"Vendor" => "Rekinza.com",
							"Reason for Return" => " ",
							"Fulfillment Model" => "FC_VOI",
							"dto center" => "abcd",
							"quantity" => $item_count
					);

	//add set of data in the encoded array
	$post_param = array('data' => json_encode(array($fields1)));
	//var_dump($post_param);
	
	$curl = curl_init($url);
	curl_setopt($curl,CURLOPT_URL, $url);
	curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($curl,CURLOPT_POST, true);
	curl_setopt($curl,CURLOPT_POSTFIELDS, $post_param);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));


	$json_response = curl_exec($curl);
	curl_close($curl);

	$res = json_decode($json_response, true);

	$status = $res['status'];
	if($status != 'true')
	{

		$a = $res['error_data'][$awb];
		$res_error = $a['type'];
	//	echo "Error is ".$res_error;
		return $res_error;

	}

	else
	{
		echo "<br>Pickup requested in Nuvo-Ex successfully<br>";
		return 200;
	}
}


function request_pickrr_for_reverse_pickup($pickup_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count)
{
	
	$url = "www.pickrr.com/api/place-order/";  
	
	$fullname = $first_name." ".$last_name;
	
	$fulladdress = $address1." ".$address2." ".$city." ".$state;

	$time = date("Y-m-d h:i:s", strtotime($currdate));
	
	$is_reverse = TRUE;
	
	$items = $item_count." Items-".$items;
	
	$fields = array(
		'auth_token' => 'b48be1f7e234bbdf88b1a09866da2088775',
		'item_name' => "$items",
		'service_type' => 'standard',
		'order_time' => "$time",
		'from_name' => "$fullname",
		'from_phone_number' => "$mobile_no",
		'from_pincode' => "$pincode",
		'from_address' => "$fulladdress",
		'to_name' => 'REKINZA-KOMAL',
		'to_phone_number' => '9871291261',
		'to_pincode' => '110017',
		'to_address' => 'B1 Basement, Square One, Saket District Center, Saket, New Delhi',
		'is_reverse' => "$is_reverse",
		'client_order_id' => "$pickup_id",
	);


	$content = json_encode($fields);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

	$json_response = curl_exec($curl);

	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$err     = curl_errno( $curl );
	$errmsg  = curl_error( $curl );
	$header  = curl_getinfo( $curl );


	/*if ( $status != 201 || $status != 200 ) {
		die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}*/


	curl_close($curl);
	$response = json_decode($json_response, true);
	$trackid = $response['tracking_id'];
	
	$error_msg = $response['err'];
	
	if($error_msg)
	{
		$response = substr($error_msg,0,9);
		$response = "Pckr-".$response;
		
		$return_array['error'] = $response;
		$return_array['request_status'] = 'FALSE';
			
	}
	else
	{
		
		$return_array['error'] = '';
		$return_array['awb'] = $trackid;
		$return_array['request_status'] = 'TRUE';
	}
	
	return $return_array;
	
	
}

function sendmail($subject, $body, $email, $cc)
{

	if (!class_exists("PHPMailer")){
	$path = __DIR__;
    require($path.'/../PHPMailer/class.phpmailer.php');
	} 
	$path = __DIR__;
	require_once($path.'/../PHPMailer/class.smtp.php');
	$exc = new phpmailerException();

	try
	{
		echo "Preparing email<br>";
		$mail = new PHPMailer(); //New instance, with exceptions enabled
		$mail->IsSMTP();                           // tell the class to use SMTP
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		//$mail->SMTPDebug  = 2;  			
		$mail->Port       = 465;                    // set the SMTP server port
		$mail->Host       = "smtp.gmail.com"; // SMTP server
		$mail->Username   = "hello@rekinza.com";     // SMTP server username
		$mail->Password   = "r3k!nz@0803";        // SMTP server password
		$mail->SMTPSecure = "ssl";
		//$mail->AddReplyTo("pratyooshm@floshowers.com","First Last");
		$mail->SetFrom("hello@rekinza.com","Rekinza");
		$to = $email;
		//$tos=explode(',',fetchVendorEmailFromName($vendorName));
		//foreach($tos as $to)
		$mail->AddAddress($to);
		$mail->AddCC($cc);
		$mail->Subject  = $subject;
		//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->WordWrap   = 50; // set word wrap
		$mail->Body = $body;
		$mail->IsHTML(true); // send as HTML	
		
		if($mail->Send() == true)
		{
			echo 'Message has been sent.';
			return 1;			
		}
		else
		{
			echo "Mailer Error: " . $mail->ErrorInfo;
			return 0;
		}
	} 
	 catch (phpmailerException $e)
	{
		echo $e->errorMessage();
		echo "Oh no";
		return 0;
	}

}

?>
