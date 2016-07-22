<?php

include '../db_config.php';

if (isset($_GET['term'])){
	
	$search_term = $_GET['term'];

	
	$query = "SELECT * FROM `sku_code_mapping` WHERE type = 'brand' and entity_name LIKE '".$search_term."%'" ;
	
	$result = mysql_query($query);

	$numresult = mysql_numrows($result);
		
	if ( $numresult > 0 )
	{
		$i = 0;
		$return_arr[] =  "We accept the brands below:";
		while ( $i < $numresult )
		{
			$brand = mysql_result($result,$i,'entity_name');
            $return_arr[] =  $brand;
			$i++;
        }	
		
		// Toss back results as json encoded array.
		echo json_encode($return_arr);
	}
	else
	{
		$query_unaccepted_brands = "SELECT * FROM `unaccepted_brands` WHERE name = '".$search_term."'" ;
	
		$result_unaccepted_brands = mysql_query($query_unaccepted_brands);

		$numresult_unaccepted_brands = mysql_numrows($result_unaccepted_brands);
			
		if ( $numresult_unaccepted_brands > 0 )
		{
			$return_arr[] =  "Sorry! We do not accept this brand";
			echo json_encode($return_arr);			
		}
		
		else
		{
			$return_arr[] =  "Woah! We haven't heard of this brand before";
			$return_arr[] =  "Please email us at hello@rekinza.com to find out if we can accept it :)";
			echo json_encode($return_arr);
		}
	}
	
}

?>