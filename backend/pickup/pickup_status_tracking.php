<?php

include 'db_config.php';

//change duration
$startdate = date('Y-m-d', strtotime("-10 days"));
$enddate = date('Y-m-d', strtotime("-0 days"));


$query = "SELECT * from thredshare_pickup WHERE pick_up_date BETWEEN '{$startdate}' AND '{$enddate}' AND waybill_number != ''";	//nuvoex waybill numbers start with RZA
echo $query;

$result = mysql_query($query);
$waybill_status_update_or_email_send_error_list = array();

while($row = mysql_fetch_assoc($result)){

	$awb = $row['waybill_number'];
	$awb = trim($awb);

	$logistics_partner = $row['logistics_partner'];
	$email = $row['email'];

	if($logistics_partner == "NuvoEx")
	{
		$status = fetch_status_nuvoex($awb);
	}
	else if($logistics_partner == "Pickrr")
	{
		$status = fetch_status_pickrr($awb);
	}
	else
	{
		$fault = "id: ".$row['id']." table-status: ".$row['status']." waybill-number: ".$awb." unknown logistics_partner.\n";
		array_push($waybill_status_update_or_email_send_error_list, $fault);
		continue;
	}
	echo "waybill is ".$awb." & status is ".$status."<br>";
	//
	if($status =="delivered")
	{
		continue;
	}
	if($row['status'] == "requested" && ($status == "scheduled"))	
	 	{
			$query1 = "UPDATE thredshare_pickup SET status = 'scheduled' WHERE waybill_number = '$awb' ";
			echo $query1."<br>";
			$result1 = mysql_query($query1);
			if ($result1 == 'TRUE')
			{
				//nothing
			}
			else
			{
				$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." query failed.\n";
				array_push($waybill_status_update_or_email_send_error_list, $fault);
			}

	 	}

	elseif(($row['status']  == 'requested' || $row['status'] == 'cancelled' || $row['status'] == 'scheduled') && ($status == "picked"))
	 	{
	 		$customer_name = $row['first_name']." ".$row['last_name'];
	 		$subject = $customer_name.", Your Items Have Been Picked Up";

	 		if($logistics_partner == "NuvoEx")
			{
				$logistic_tracking_link = "http://nuvoex.com/awb_detail.html?awb=".$awb;
			}
			else if($logistics_partner == "Pickrr")
			{
				$logistic_tracking_link = "http://pickrr.com/tracking/#?tracking_id=".$awb;
			}

			$html = file_get_contents('http://www.rekinza.com/emails/pickup/items-picked-up.html');
			
			$html = str_replace("{customer_name}", $customer_name, $html);
			$html = str_replace("{tracking_number}", $awb, $html);
			$html = str_replace("{track_link}", $logistic_tracking_link, $html);
			
			echo "link is".$logistic_tracking_link;

			$body = $html;

			$mobile = $row['mobile'];
			$sms_content = "Hi {$customer_name}, Your items are on their way to us, and we can hardly wait! It can take 7-10 days for your package to reach us. Your can track your package here {$logistic_tracking_link} Cheers, Team Rekinza";

			$flag1 = sendSMS($sms_content, $mobile);
			$flag2 = sendmail($subject, $body, $email);	//cancelled mail
			if($flag1 || $flag2)				//if mail is successful
			{
				$today = date("Y-m-d");
				$query1 = "UPDATE thredshare_pickup SET status = 'picked-up', pick_up_date = '$today' WHERE waybill_number = '$awb' ";
				echo $query1."<br>";
				$result1 = mysql_query($query1);
				if ($result1 == 'TRUE')
				{
					//do nothing;
				}
				else
				{
					$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." query failed.\n";
					array_push($waybill_status_update_or_email_send_error_list, $fault);
				}
			}
			else
			{

				$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." email failed no query ran.\n";
				array_push($waybill_status_update_or_email_send_error_list, $fault);
			}
	 	}

 	elseif($row['status'] != "cancelled" && ($status == "cancelled" ))
 	{

 		$customer_name = $row['first_name']." ".$row['last_name'];
 		$subject = $customer_name.", Give Us A Chance";
		$html = file_get_contents('http://www.rekinza.com/emails/pickup/pickup-cancellation.html');
		$html = str_replace("{customer_name}", $customer_name, $html);
		$body = $html;

		$mobile = $row['mobile'];
		$sms_content = "Hi {$customer_name}, this is Vidisha from Rekinza. You had shown an interest in selling with us by scheduling a pick up. However I noticed that your pickup did not happen. Please do let me know if I can reschedule your pickup for you. Feel free to call me in case you have any queries.(+91-9810961177)";

		$flag1 = sendSMS($sms_content, $mobile);
		$flag2 = sendmail($subject, $body, $email);	//cancelled mail
		if($flag1 || $flag2)				//if mail is successful
		{
			$query1 = "UPDATE thredshare_pickup SET status = 'cancelled' WHERE waybill_number = '$awb' ";
			echo $query1."<br>";
			$result1 = mysql_query($query1);
			if ($result1 == 'TRUE')
			{
				
			}
			else
			{
				$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." query failed.\n";
				array_push($waybill_status_update_or_email_send_error_list, $fault);
			}
		}
		else
		{
			$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." email failed no query ran.\n";
			array_push($waybill_status_update_or_email_send_error_list, $fault);
		}

 	}

 	elseif ($status == "error")
	{

		$fault = "id: ".$row['id']." table-status: ".$row['status']." ".$logistics_partner." status: ".$status." waybill-number: ".$awb." needs attention.\n";
		array_push($waybill_status_update_or_email_send_error_list, $fault);
			
 	}
}	//while ends

if (!empty($waybill_status_update_or_email_send_error_list))
{
     
	$email = "stuti@rekinza.com";
	$subject = "CRON JOB ERRORS -- pickup status";
	$fault_string = implode("<br>",$waybill_status_update_or_email_send_error_list);
	$body = $fault_string;
    sendmail($subject , $body, $email);
}



function fetch_status_nuvoex($waybill)
{

	$waybill = trim($waybill);

	$url = "http://rekinza:123456@api.nuvoex.com/api/awb/history/".$waybill;
		
	$ch = curl_init(); 

	// set url 
	curl_setopt($ch, CURLOPT_URL, $url); 

	//return the transfer as a string 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

	// $output contains the output string 
	$content = curl_exec($ch); 

	// close curl resource to free up system resources 
	curl_close($ch); 

	$content = json_decode($content, true);
	//var_dump($content["history"]);//[0]);
	$history = $content["history"][0];
	$nuvoex_status = $history['client_status_code'];
	
	if($nuvoex_status != "DEL" && $nuvoex_status != "DTC")
	{					
		if($nuvoex_status == "DR" || $nuvoex_status == "DSP" || $nuvoex_status == "SCH" || $nuvoex_status == "PS")
		{
			return "scheduled";
		}
		elseif($nuvoex_status == "CAN" || $nuvoex_status == "CNA")
		{
			return "cancelled";
		}
		elseif($nuvoex_status == "DTO" || $nuvoex_status == "INT" || $nuvoex_status == "ICS" || $nuvoex_status ==  "PC" || $nuvoex_status ==  "PFD" || $nuvoex_status ==  "TBS")
		{
			return "picked";
		}
		elseif($nuvoex_status == "CB" || $nuvoex_status == "DBC" || $nuvoex_status == "DFR" || $nuvoex_status == "DPR" || $nuvoex_status == "RTO" || $nuvoex_status == "RQC" || $nuvoex_status == "RET" || $nuvoex_status == "RBC" || $nuvoex_status == "PFR" || $nuvoex_status == "NA" || $nuvoex_status == "LOS")
		{
			return "error";
		}
	}
	else
	{
		return "delivered";
	}

}

function fetch_status_pickrr($waybill)
{	
	$waybill = trim($waybill);

	$url = "http://www.pickrr.com/api/tracking/$waybill/";
  	$options = array(
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_USERAGENT      => "spider", // who am i
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    CURLOPT_TIMEOUT        => 120,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    CURLOPT_SSL_VERIFYPEER => false,
    );

    $ch = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $contentold = curl_exec( $ch );
	$err = curl_errno( $ch );
	$errmsg  = curl_error( $ch );
	$header  = curl_getinfo( $ch );
	curl_close( $ch );

	$header['errno']   = $err;
	$header['errmsg']  = $errmsg;
	$header['content'] = $contentold;

	$content = json_decode($contentold, true);

	$final_status = $content['final_status'];
	$final_status_date = $content['final_status_date'];
	//$delivery_status_date = $content['delivery_status_date'];
	$transit_status_date = $content['transit_status_date'];
	$warehouse_status_date = $content['warehouse_status_date'];
	$pickup_status_date = $content['pickup_status_date'];
	$order_status_date = $content['order_status_date'];
	$cancelled = $content['is_cancelled']; //make it returned and check after cancelled
	
	if($cancelled){
		return "cancelled";
	}
	elseif( !is_null($final_status)){
		if($final_status == "delivered"){
		return "delivered";
		}
		if($final_status == "rto"){
		return "error";
		}
	}			    		
	elseif( !is_null($transit_status_date))
	{
		return "picked";
	}	
	elseif(!is_null($warehouse_status_date))
	{
		return "picked";
	}
	elseif (!is_null($pickup_status_date)) 
	{
			return "picked";
	}
	elseif (!is_null($order_status_date)) 
	{
			return "scheduled";
	}
}

function sendmail($subject, $body, $email){


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
?>			