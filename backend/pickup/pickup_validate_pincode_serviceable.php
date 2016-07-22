<?php

include 'db_config.php';

$pincode =$_POST['pincode'];

$query = "SELECT region FROM `pincodes_nuvoex` WHERE pincode = '".$pincode."'" ;

$result = mysql_query($query);

$numresult = mysql_numrows($result);


if ($numresult > 0)   //Check if pincode entered is serviceable
{
    //Do nothing;    
}
else
{
	$query = "SELECT city FROM `pincodes_pyck` WHERE pincode = '".$pincode."'" ;

	$result = mysql_query($query);

	$numresult = mysql_numrows($result);
	
	if ($numresult > 0) 
	{
		//Do nothing;    
	}
	else
	{
		echo "Sorry! This pincode is currently not serviceable";
	}
}

?>