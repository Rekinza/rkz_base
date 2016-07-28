<?php
include 'db_config.php';
include '../../app/Mage.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';
$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ( $numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "cashout panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
		Mage::init();
		$customer = Mage::getModel("customer/customer");
		  $customer->setWebsiteId(Mage::app()->getWebsite()->getId()); 
		  
		$customer_email_id = $_GET['email_id'];
		//$customer_name = $_GET['name'];
		$cashout_id = $_GET['cashout_id'];
		$email_type = $_GET['email_type'];
		$customer->loadByEmail($customer_email_id);
		$firstname = $customer->getfirstname(); 
		if (strlen($firstname) < 2)
		  {
			$lastname = $customer->getlastname();
			$customer_name = $firstname." ".$lastname;
		  }
		  else
		  {
			$customer_name = $firstname;
		  }
		//email type will always be approved though
		if ($email_type == "approved")
		{
			//Please fill the subject and html url accordingly
			$subject = $customer_name.", your cashout is complete";
					$html = file_get_contents('http://rekinza.com/emails/cashout/cashout_done.html');
			
			$html = str_replace("{customer_name}", $customer_name, $html);
			
			$body = $html;
			
			echo $body;
		}
		else{
			
			echo "blank email";
		}
?>

<html>
<form action = "cashout_send_email.php" method = "POST" target="_blank">
<td><input type = "text" value = '<?php echo $customer_email_id ?>' name = "customer_email_id"></td><br>
<td><input type = "text" value = '<?php echo $subject ?>' name = "subject"></td><br>
<td><textarea name = "body"><?php echo $body ?></textarea></td><br>
<td><input type = "text" value = '<?php echo $cashout_id ?>' name = "cashout_id" hidden = true></td><br>
<td><input type = "text" value = '<?php echo $email_type ?>' name = "email_type" hidden = true></td><br>
<td><input type = "Submit" value = "Send Email!"></td>
</form>
		<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
</html>
<?php
	} //panelif ends
	else {
	echo "Sorry! You are not authorised.";
	}
} //numrows if ends
else {
	echo "Sorry! You are not authorized.";
}
?>