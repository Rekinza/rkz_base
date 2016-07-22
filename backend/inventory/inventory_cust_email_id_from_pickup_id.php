<?php

include 'db_config.php';

//include '../../app/Mage.php';

//Mage::app();

$pickup_id =$_POST['pickup_id'];

$query = "SELECT email from thredshare_pickup WHERE id = '".$pickup_id."' ";

$res = mysql_query($query);

$customer_email_id = mysql_result($res,0,'email');

mysql_close();

if ($customer_email_id)
{
	echo $customer_email_id;
}
else
{
	echo $pickup_id;
}
	
	
?>
