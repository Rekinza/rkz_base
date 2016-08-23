<?php

include '../../app/Mage.php';
Mage::app();
include 'db_config.php';

$mail = $_POST['mail'];
$query = "SELECT * from thredshare_pickup where email = '$mail' LIMIT 1";
$result = mysql_query($query);
$numresult = mysql_numrows($result);
if ($numresult > 0) {
    $first_name = mysql_result($result, 0, 'first_name');
    $last_name = mysql_result($result, 0, 'last_name');
    $mobile = mysql_result($result, 0, 'mobile');
    $address1 = mysql_result($result, 0, 'address1');
    $address2 = mysql_result($result, 0, 'address2');
    $city = mysql_result($result, 0, 'city');
    $state = mysql_result($result, 0, 'state');
    $pincode = mysql_result($result, 0, 'pincode');

    echo json_encode( array
    	("first_name" => $first_name,
			"last_name" => $last_name,
			"mobile" => $mobile,
			"address1" => $address1,
			"address2" => $address2,
			"city" => $city,
			"state" => $state,
			"pincode" => $pincode));

} else {
    exit(0);
}
