<?php
include 'db_config.php';
$path = __DIR__;
require_once $path.'/../../app/Mage.php';
Mage::app();
$today = date("Y-m-d");
$today = date('Y-m-d H:i:s', strtotime($today));
$tomorrow = strtotime ( '+24 hours' , strtotime ($today )) ;  //Find tomorrow's date;
$tomorrow = date('Y-m-d', $tomorrow);
$query = "SELECT id FROM thredshare_returns WHERE pickup_date = '$tomorrow' AND waybill_number = '' AND status = 'requested'";
$result = mysql_query($query);
$numresult = mysql_numrows($result);
$return_schedule_error_list = array();
echo $numresult;
if($numresult > 0)
{
	$i = 0;
	while ( $i < $numresult )
	{
		$return_id = mysql_result($result,$i,'id');
		echo $return_id;
		
		$query_fetch_returns_details = "SELECT * FROM thredshare_returns WHERE id = '$return_id'";
			
		$result_fetch_returns_details = mysql_query($query_fetch_returns_details);
			
		//mysql_error();	
		
		if (mysql_numrows($result_fetch_returns_details) != 0)
		{
			$mobile_no = mysql_result($result_fetch_returns_details,0,'mobile');
			$address1 = mysql_result($result_fetch_returns_details,0,'address1');
			$address2 = mysql_result($result_fetch_returns_details,0,'address2');
			$city = mysql_result($result_fetch_returns_details,0,'city');
			$state = mysql_result($result_fetch_returns_details,0,'state');
			$pincode = mysql_result($result_fetch_returns_details,0,'pincode');
			$order_id = mysql_result($result_fetch_returns_details,0,'order_id');
			$email_id = mysql_result($result_fetch_returns_details,0,'email');
			$amount = mysql_result($result_fetch_returns_details,0,'amount');
			$items = mysql_result($result_fetch_returns_details,0,'items');
			
			echo $items;
			//get firstname and lastname
			
			$collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $order_id);
			if ($collection->count() ==0)   //Check if order ID entered is valid
			{
				$order_id = $order_id."-1";
				$collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $order_id);
				if ($collection->count() ==0)   //Check if order ID entered is valid
				{
					$fault = "Order ID :".$order_id." Unable to fetch details. Not scheduled<br>";
					array_push($return_schedule_error_list, $fault);
					
					$i++;
					continue;
				}				
			}
			
			$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
			
			$first_name = $order->getBillingAddress()->getFirstname();
			$last_name = $order->getBillingAddress()->getLastname();
			
		
			if($items)
			{
				$list_of_items = explode(", ", $items);	
				$items_name = '';
				
				if($list_of_items)
				{
					foreach($list_of_items as $item)
					{
						$_sku = $item;
						$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_sku);
						$items_name .= $_product->getName();
						$items_name .= ", ";
					}
				}
				
					// else
					// {
					// 	$item_count = 1;
					// }
			}
			else
			{
				$items_name = "1 Apparel";
			}
			$items = $items_name;
			
			$response_nuvoex = request_nuvoex_for_reverse_pickup($return_id, $order_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);			
			if($response_nuvoex['request_status'] == 'FALSE')
			{
				//Save the error
				
				echo "<br>Updating Nuvo Ex error";
				
				$error_response = $response_nuvoex['error'];
				
				$query_update_waybill_error = "UPDATE thredshare_returns SET waybill_number = '$error_response' WHERE id = '$return_id'";
				
				$result_update_waybill_error = mysql_query($query_update_waybill_error);
				
				//Schedule with Pickrr
				
				$response_pickrr = request_pickrr_for_reverse_pickup($order_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);
				if($response_pickrr['request_status'] == 'FALSE')
				{
					//Save the error
					
					echo "<br>Updating Pickrr error";
					
					$error_response = $response_pickrr['error'];
					
					$query_update_waybill_error = "UPDATE thredshare_returns SET waybill_number = '$error_response' WHERE id = '$return_id'";
					
					$result_update_waybill_error = mysql_query($query_update_waybill_error);
					
					//Add to error list
					$fault = "email ID: ".$email_id." Unable to schedule with any partner\n";
					array_push($return_schedule_error_list, $fault);
					
					
				}
				else
				{
					echo "<br>Updating Pickrr success";
					
					$awb = $response_nuvoex['awb'];
					
					//Save the AWB, update status and logistics partner
					$query_update_pickup_status_and_awb_and_partner = "UPDATE thredshare_returns SET status = 'scheduled', logistics_partner = 'Pickrr', waybill_number = '$awb' WHERE id = '$return_id'";
				
					$result_update_pickup_status_and_awb_and_partner = mysql_query($query_update_pickup_status_and_awb_and_partner);
					
				}
			}
			else
			{
				echo "<br>Updating NuvoEx success";
				
				$awb = $response_nuvoex['awb'];
				
				//Save the AWB, update status and logistics partner
				$query_update_pickup_status_and_awb_and_partner = "UPDATE thredshare_returns SET status = 'scheduled', logistics_partner = 'NuvoEx', waybill_number = '$awb' WHERE id = '$return_id'";
			
				$result_update_pickup_status_and_awb_and_partner = mysql_query($query_update_pickup_status_and_awb_and_partner);
			} 
			
		}
		else
		{
			//Add to error list
			$fault = "Return ID :".$return_id." Unable to fetch details. Not scheduled<br>";
			array_push($return_schedule_error_list, $fault);
					
		}
	$i++;
	}
	
	if (!empty($return_schedule_error_list))
	{
		$email = "komal@rekinza.com";
		$cc = "stuti@rekinza.com";
		$subject = "CRON JOB ERRORS -- returns scheduling";
		$fault_string = implode("<br>",$return_schedule_error_list);
		$body = $fault_string;
		sendmail($subject , $body, $email, $cc);
	}
	
	
}
else
{
	echo "No data to process";
}
function request_pickrr_for_reverse_pickup($order_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count)
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
		'client_order_id' => "$order_id",
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
function request_nuvoex_for_reverse_pickup($return_id, $order_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count)
{
	// Currently some pincodes are not operational for NuvoEX
	
	$non_serviceable_pincodes = array();
	
	$non_serviceable_pincodes = array_fill('410210','400612','400601','400602','400603','400604','400605','400606','400607','400608','400609','400610','400614','400615','400701','400703','400705','400706','400708','400709','400710','401105','401101','401104','401107','410206','410209');
	
	if (in_array($pincode,$non_serviceable_pincodes))
	{
		$return_array['error'] = 'non-servicable pincode';
		$return_array['awb'] = '';
		$return_array['request_status'] = 'FALSE';
		
		echo  "Thane - Failed with Nuvo Ex ID ".$pickup_id;
		
		return $return_array;
		
	}
	
	
	$awb = get_and_reserve_first_available_nuvoex_waybill();  //Get and reserve first available waybill number
				
	$query_set_waybill_number_for_return_id = "UPDATE thredshare_returns SET waybill_number = '$awb' WHERE id = '$return_id'";  //Reserve waybill number for this pick up ID
		
	$result_set_waybill_number_for_return_id = mysql_query($query_set_waybill_number_for_return_id);
	
	$response = send_reverse_pickup_request_to_nuvoex($awb, $order_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count);
			
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
	
	echo "AWB is ".$awb."<br>";
	
	return $awb;
}
					
function send_reverse_pickup_request_to_nuvoex($awb, $return_id, $mobile_no, $address1, $address2, $city, $state, $pincode, $first_name, $last_name, $items, $item_count)
{
	$url = "http://rekinza:123456@api.nuvoex.com/api/manifest/upload";
	
	$pkgvalue = 1000 * $item_count;
	$weight = 250 * $item_count;
		
	$fields1 = array(
							"AWB" => $awb,
							"CLIENT ORDER NO" => $return_id,
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
	echo "\n";
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
		echo "Error is ".$res_error;
		return $res_error;
	}
	else
	{
		echo "Requested in Nuvo Ex successfully<br>";
		return 200;
	}
}//setwaybill function ends
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