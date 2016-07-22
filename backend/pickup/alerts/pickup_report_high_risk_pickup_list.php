<?php

$db_path = __DIR__;

$db_path = $db_path.'/../db_config.php';

include $db_path;

$today = $today = date("Y-m-d");

fetch_and_email_high_risk_pickup_list($today);

function fetch_and_email_high_risk_pickup_list($today)
{

	$start_time = date('Y-m-d', strtotime("-1 days"));
	
	$start_time = strtotime ( '+17 hours' , strtotime ($start_time )) ;
	$start_time = date('Y-m-d H:i:s', $start_time);

	$end_time = date('Y-m-d H:i:s', strtotime($today));
	
	$end_time = strtotime ( '+16 hours' , strtotime ($end_time )) ;
	$end_time = date('Y-m-d H:i:s', $end_time);

	$end_time = strtotime ( '+59 minutes' , strtotime ($end_time )) ;
	$end_time = date('Y-m-d H:i:s', $end_time);

	$end_time = strtotime ( '+59 seconds' , strtotime ($end_time )) ;
	$end_time = date('Y-m-d H:i:s', $end_time);
	
	$query =  "SELECT id,first_name,last_name, email, mobile FROM thredshare_pickup WHERE creation_date BETWEEN '$start_time' AND '$end_time' AND item_count >=20";
	
	$result = mysql_query($query);

	mysql_error();

	$numresult = mysql_numrows($result);

	if ($numresult >0)
	 {
		$alert_message_array = array();
		
		$i = 0;
		
		while ($i < $numresult)
		{
			$pickup_id = mysql_result($result,$i,'id');
			
			$first_name = mysql_result($result,$i,'first_name');
			
			$last_name = mysql_result($result,$i,'last_name');
			
			$email = mysql_result($result,$i,'email');
			
			$mobile = mysql_result($result,$i,'mobile');
			
			$alert_message = "<tr><td>pick up id: ".$pickup_id."</td><td> email : ".$email."</td><td> Name: ".$first_name." ".$last_name."</td><td> Mobile: ".$mobile."</td></tr>\n";

			array_push($alert_message_array, $alert_message);
			
			$i++;
		
		}
		$email = "vidisha@rekinza.com";
		$cc = "stuti@rekinza.com";
		$subject = "CRON JOB - High Risk Pick Ups";
		$alert_message_array = implode("<br>",$alert_message_array);
		$body = $alert_message_array;
		sendmail($subject , $body, $email,$cc);
				
	} 

}


function sendmail($subject, $body, $email,$cc)
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