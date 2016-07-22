<?php

$db_path = __DIR__;

$db_path = $db_path.'/../db_config.php';

include $db_path;

$startdate = date('Y-m-d', strtotime("-7 days"));

$today = $today = date("Y-m-d");

$today = date('Y-m-d H:i:s', strtotime($today));

$yesterday = date('Y-m-d', strtotime("-1 days"));

fetch_and_email_pending_pickups($startdate,$yesterday,$today);

function fetch_and_email_pending_pickups($startdate,$yesterday,$today)
{
	$query =  "SELECT order_id, email, pickup_date, waybill_number FROM thredshare_returns WHERE pickup_date BETWEEN '$startdate' AND '$yesterday' AND status = 'scheduled' ORDER BY pickup_date";
		
	$result = mysql_query($query);

	mysql_error();

	$numresult = mysql_numrows($result);
	
	if( $numresult > 0 )
	{
		$pending_array = array();
		
		$i = 0;
		while ( $i < $numresult)
		{	
			$order_id = mysql_result($result,$i,'order_id');
			$email = mysql_result($result,$i,'email');
			$waybill_number = mysql_result($result,$i,'waybill_number');
			$pickup_date = mysql_result($result,$i,'pickup_date');
			
			$date_diff = ceil(strtotime($today) - strtotime($pickup_date))/86400;
			
			$pending_message = "<tr><td>order id: ".$order_id."</td><td> email : ".$email."</td><td> waybill number: ".$waybill_number."</td><td> Delay in Days: ".$date_diff."</td></tr>";
			array_push($pending_array, $pending_message);		
			
			$i++;
		}			
		if (!empty($pending_array))
		{
			 
			$email = "komal@rekinza.com";
			$cc = "stuti@rekinza.com";
			$subject = "CRON JOB - Delayed returns";
			$pending_array_string = implode("<br>",$pending_array);
			$body = $pending_array_string;
			sendmail($subject , $body, $email, $cc);
		}
		
	}

}

function sendmail($subject, $body, $email, $cc)
{


	if (!class_exists("PHPMailer")){
	$path = __DIR__;
    require($path.'/../../PHPMailer/class.phpmailer.php');
	} 
	$path = __DIR__;
	require_once($path.'/../../PHPMailer/class.smtp.php');
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
				//$mail->AddReplyTo("something@rekinza.com","First Last");
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
					echo 'Message has been sent.<br>';
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