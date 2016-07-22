<?php

include 'db_config.php';

$type =$_POST['type'];
$sub_type =$_POST['sub_type'];
$category =$_POST['category'];

if($type)
{
	$get = mysql_query("SELECT entity_name from sku_code_mapping WHERE type = 'sub-type' AND parent = '".$type."' ");
}
else if($sub_type)
{
	$get = mysql_query("SELECT entity_name from sku_code_mapping WHERE type = 'category' AND parent = '".$sub_type."' ");	
}
else if($category)
{
	$get = mysql_query("SELECT entity_name from sku_code_mapping WHERE type = 'sub-category' AND parent = '".$category."' ");	
}

$option = '';
while($row = mysql_fetch_assoc($get))
{
  $option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
}
mysql_close();

	echo $option;

?>