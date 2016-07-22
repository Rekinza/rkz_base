<?php

include 'db_config.php';
$path = __DIR__;
include $path.'/../../app/Mage.php';
Mage::app();

 $today = date('Y-m-d');

 $query = "SELECT * from thredshare_pickup where processing_date < '$today' and status = 'processed'";
 echo $query;
 $result = mysql_query($query);
 $count = mysql_numrows($result);
 echo $count;

 $waybill_status_update_or_email_send_error_list = array();

 while($result_set = mysql_fetch_assoc($result))
 {
 	
 	$pickup_id = $result_set['id'];

 	//AND query to ensure every accepted item is priced.
 	$pickup_query = "SELECT * from inventory where qc_status = 'accepted' and pickup_id = '$pickup_id' and suggested_price > 199 and retail_value > 199";
 	$pickup_result = mysql_query($pickup_query);
 	$pickup_count = mysql_numrows($pickup_result);

 	$accepted_query = "SELECT * from inventory where qc_status = 'accepted' and pickup_id = '$pickup_id'";
 	$accepted_result = mysql_query($accepted_query);
 	$accepted_count = mysql_numrows($accepted_result);
 	
 	if($accepted_count > 0 && ($accepted_count == $pickup_count))
 	{
 		
 		//$email_type = "priced";
 		$customer_name = $result_set['first_name'];
 		$email = $result_set['email'];

		$subject = $customer_name.", We Are Almost Done!";
		
		$html = file_get_contents('http://www.rekinza.com/emails/pickup/items-priced.html');

		//$result_set2 = mysql_fetch_all($pickup_result, MYSQLI_ASSOC);
		$i = 0;
		while ( $i < $pickup_count )
		{
			$product_name = mysql_result($pickup_result,$i,'product_name');
			$brand = mysql_result($pickup_result,$i,'brand');
			//$rejection_reason = mysql_result($pickup_result,$i,'rejection_reason');
			$product_price = mysql_result($pickup_result,$i,'suggested_price');
			
			//for msrp;
			$query_msrp = "SELECT * from msrp_commission_slabs where $product_price > lower_limit and $product_price <= upper_limit" ;

			$result_msrp = mysql_query($query_msrp);
			if(mysql_numrows($result_msrp) > 0)
			{
				$row = mysql_fetch_assoc($result_msrp);
				if($row['fixed_commission'])
				{
					$msrp = $product_price - $row['fixed_commission'];
				}
				elseif ($row['percent_commission']) 
				{
					$msrp = (1 - $row['percent_commission']/100) * $product_price;
				}
				else
				{
					mage::log("error");
				}
				//exit(0);
			}
			else
			{
				$msrp = "Price not found";
			}
			$index = $i+1;
			$html = str_replace("<tr><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td></tr>", 
			"<tr><td class = \"items\">".$index."</td><td class = \"items\">".$product_name."</td><td class = \"items\">".$brand."</td><td class = \"items\">".$product_price."</td><td class = \"items\">".$msrp."</td></tr>"."<tr><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td></tr>",$html);
			
			$i++;
		}
		
		$html = str_replace("<tr><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td><td class = \"items\"></td></tr>","",$html);	
		$html = str_replace("{customer_name}", $customer_name, $html);

		$body = $html;
		$flag = sendmail($subject, $body, $email);
		if($flag)
		{
			$query1 = "UPDATE thredshare_pickup SET status = 'priced' WHERE id = '$pickup_id' ";
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
 	elseif($accepted_count == 0)
 	{
 		$query1 = "UPDATE thredshare_pickup SET status = 'priced' WHERE id = '$pickup_id' ";
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
 	if (!empty($waybill_status_update_or_email_send_error_list))
	{    
		$email = "stuti@rekinza.com";
		$subject = "CRON JOB ERRORS -- pickup status";
		$fault_string = implode("<br>",$waybill_status_update_or_email_send_error_list);
		$body = $fault_string;
	    sendmail($subject , $body, $email);
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

?>