<?php

include 'db_config.php';

if (isset($_GET['term'])){
	
	$search_term = $_GET['term'];

	
	$query = "SELECT * FROM `sku_code_mapping` WHERE type = 'brand' and entity_name LIKE '".$search_term."%'" ;
	
	$result = mysql_query($query);

	$numresult = mysql_numrows($result);
		
	if ( $numresult > 0 )
	{
		$i = 0;
		while ( $i < $numresult )
		{
			$brand = mysql_result($result,$i,'entity_name');
            $return_arr[] =  $brand;
			$i++;
        }	
		
    // Toss back results as json encoded array.
    echo json_encode($return_arr);
	}
	
}

?>