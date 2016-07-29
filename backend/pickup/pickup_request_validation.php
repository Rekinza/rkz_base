<?php
include '../../app/Mage.php';
include 'db_config.php';
Mage::app();
$email = $_POST['email'];
$date = $_POST['date'];
$newdt = date("Y-m-d", strtotime($date));
$query = "SELECT id from thredshare_pickup where email = '$email' and pick_up_date = '$newdt'";
$result = mysql_query($query);
$id_found = mysql_result($result, 0, 'id');
$numrow = mysql_numrows($result);
if($id_found)
{
	echo "You have already placed a request for pickup from this email address and on this particular date. If you wish you make an modification, please email us at blahblah";
}
else
{
	exit(0);
}
?>