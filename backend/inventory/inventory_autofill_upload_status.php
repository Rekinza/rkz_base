<?php

include 'db_config.php';

if (isset($_GET['term'])){
	
	$search_term = $_GET['term'];
	
	$query = "SELECT * FROM `inventory_upload_status` WHERE status LIKE '".$search_term."%'" ;
	
	$result = mysql_query($query);

	$numresult = mysql_numrows($result);
			
	if ( $numresult > 0 )
	{
		$i = 0;
		while ( $i < $numresult )
		{
			$status = 'photo taken';//mysql_result($result,$i,'status');
            $return_arr[] =  $status;
			$i++;
        }	
		
    // Toss back results as json encoded array.
    echo json_encode($return_arr);
	}
	
}

?>