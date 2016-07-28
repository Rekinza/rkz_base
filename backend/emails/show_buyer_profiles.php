<?php

include '../db_config.php';

$query = "SELECT data FROM buyer_profiles WHERE 1";
				
$result = mysql_query($query);

mysql_error();

$numresult = mysql_numrows($result);

$i = 0;

while ($i < $numresult)
{
	$data = mysql_result($result,$i,'data');
	
	echo $data."<br>";
	
	$i++;
	
}
				


?>