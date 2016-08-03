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
	echo "We already have a pickup request from this email address for this particular date. If you wish you make an chnages, please email us at hello@rekinza.com";
}
else
{
	exit(0);
}
?>