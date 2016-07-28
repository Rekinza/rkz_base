<?php
include '../db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "returns panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	$customer_email_id = $_POST['customer_email_id'];
	$body = $_POST['body'];
	$subject = $_POST['subject'];
	$returns_id = $_POST['returns_id'];   //Required to update status of pick up
	$email_type = $_POST['email_type'];

	require('../PHPMailer/class.phpmailer.php');
	require_once('../PHPMailer/class.smtp.php');
	?>
	<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
	<?php

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
				$to = $customer_email_id;
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
						$status = getStatusFromEmailType($email_type);
						//when email sent from any state, state in table is automatically updated to "approved"
						$query = "UPDATE thredshare_returns SET status = '$status' WHERE id = '$returns_id' ";
						echo $status;
						echo $returns_id;
		
						$result = mysql_query($query);
						
						
						if ($result == 'TRUE')
						{
							echo 'Record Updated Successfully';
						}
						else
						{
							echo 'Record Update Failed';
						}
					
					}
					else
					{
						echo "Oh damn";
					}
			} 
			catch (phpmailerException $e)
			{
				echo $e->errorMessage();
				echo "Oh no";
			}

	

	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //numrows ends
else {
	echo "Sorry! Not authorized.";
}

//not required but left the function as it is so incase other cases come up later
	function getStatusFromEmailType($email_type)
	{
		if ($email_type =='picked-up')
		{
			return 'picked up';
		}
		
		else if ($email_type == 'received')
		{
			return 'received';
		}	
	}
?>