<?php

include '../db_config.php';
function ac_level($blockname){

	$query = "SELECT * from panel_access where block = '$blockname'";
	$result = mysql_query($query);
	$block_access = mysql_result($result, 0, 'block_access');
	return $block_access; 
}

?>