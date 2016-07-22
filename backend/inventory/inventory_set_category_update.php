<?php

include 'db_config.php';

$sku = $_POST['sku'];
$category = $_POST['category'];
$sub_category = $_POST['sub_category'];

$query = "UPDATE `inventory` SET category = '$category', sub_category = '$sub_category' WHERE sku_name = '$sku'";

echo $query."<br>";

$result = mysql_query($query);

if ($result == TRUE)
{
	echo 'Record updated successfully';
}
else
{
	echo 'Record update failed';
}

?>