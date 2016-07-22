<?php

include '../../../app/Mage.php';

Mage::app();

include 'db_config.php';

$pincode =$_POST['pincode'];

$query = "SELECT * FROM `pincodes_pyck` WHERE pincode = '$pincode'";

$result = mysql_query($query);

$numresult = mysql_numrows($result);

if($numresult > 0)
{
	
	
		$city = mysql_result($result,0,'city');
		$state =mysql_result($result,0,'state');
		//echo $state."<br>";
		$regionModel = Mage::getModel('directory/region')->loadByCode($state,'IN');
		$regionId = $regionModel->getId();
		
			//$regionCollection = Mage::getModel('directory/region_api')->items('IN');
	//var_dump($regionCollection);
		
		echo json_encode(
		array("city" => $city,
		"state_code" => $regionId,
		"zip" => $pincode)
		);
}

?>