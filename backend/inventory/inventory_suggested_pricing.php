<?php
include '../../app/Mage.php';
Mage::app();
include 'db_config.php';

$brand = $_POST['brand'];
$condition = $_POST['condition'];
$price = $_POST['price'];

$query = "SELECT price_bucket_id from brand_category_pricing_map where brand = '$brand'";
$result = mysql_query($query);

$numresult = mysql_numrows($result);

if( $numresult > 0)
{
	$price_bucket_id = mysql_result($result,0,'price_bucket_id');
}
else
{
	$query = "SELECT value from properties_table where property_name = 'luxury_price_lower_limit'";
	$result = mysql_query($query);
	
	$luxury_price_lower_limit = mysql_result($result,0,'value');
		
	if($price > $luxury_price_lower_limit)
	{
		$price_bucket_id = 2;
	}
	else
	{
		$price_bucket_id = 3;
	}
}

$query2 = "SELECT * from category_condition_pricing_map where id = '$price_bucket_id' ";
$result2 = mysql_query($query2);
$numresult = mysql_numrows($result2);

$percentage = mysql_result($result2,0,$condition);

$price_percentage = 100 - $percentage;
$suggested_price = $price_percentage/100 * $price;

$special_price = getprice($suggested_price);

echo (json_encode(array("off" => $percentage, "special_price" => $special_price)));
exit(0);


function getprice($price)
{
	
	
	$whole_number = round($price);
	
	$num = strlen($whole_number);
	$last_digit = $whole_number % 10;

	if($last_digit > 5 && $last_digit <= 9)
	{
		$last_digit = 5;
		$for_new_whole_number = str_split($whole_number, $num - 1);
		$part_number = $for_new_whole_number[0];

		$complete_number = $part_number.$last_digit;
	}
	elseif ($last_digit > 0 && $last_digit < 5) 
	{
		$last_digit = 0;
		$for_new_whole_number = str_split($whole_number, $num - 1);
		$part_number = $for_new_whole_number[0];

		$complete_number = $part_number.$last_digit;
		$complete_number = $complete_number - 1;

	}
	elseif ($last_digit == 0) 
	{
		
		$complete_number = $whole_number -1;

	}
	elseif ($last_digit == 5) 
	{
		$complete_number = $whole_number;
	}
	else
	{
		//nothing
	}	

	//if block ends

	$rem = $complete_number % 100;

	if($rem < 10 )
	{
		$final_number = $complete_number - ($rem + 1); 
	}	
	else
	{
		$final_number = $complete_number;
	}	
		
	return $final_number;


}

?>