<?php
include 'db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "inventory panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
		$pickup_id = $_GET['pickup_id'];
		$item_count = $_GET['item_count'];
		$first_name = $_GET['first_name'];
		$last_name = $_GET['last_name'];
		$address1 = $_GET['address1'];
		$address2 = $_GET['address2'];
		$city = $_GET['city'];
		$state = $_GET['state'];
		$pincode = $_GET['pincode'];
		$mobile = $_GET['mobile'];
		$return_payment_status = $_GET['return_payment_status'];

		$fullname =  $first_name." ".$last_name;
		$zipcode = $pincode;
		$fulladdress = $address1." ".$address2." ".$city." ".$state;
		$currdate = date("Y-m-d");
		$currdate = $currdate." "."14:00";
		$time = date("Y-m-d h:i:s", strtotime($currdate));

		$query = "SELECT value from properties_table WHERE property_name = 'average_item_value_estimate'";

		$result = mysql_query($query);
			
		$average_item_value = mysql_result($result,0,'value');
			
		$items_value = $item_count * $average_item_value ;

		if( ($return_payment_status == "Prepaid requested") || ($return_payment_status == "Prepaid paid" ))
		{
			$collectible_amount = 0;
			$ordermethodpyck = "NONCOD";
		}
		else
		{
			$ordermethodpyck = "COD";
			
			$query = "SELECT value from properties_table WHERE property_name = 'seller_return_cod_charge'";

			$result = mysql_query($query);
			
			$collectible_amount = mysql_result($result,0,'value');
		}

		$order_items = $item_count." apparels";

		$currdate = date("Y-m-d");
		$currdate = $currdate."T14:00:00Z";
		$invoice_date = date("Y-m-d");

		$url = "http://www.pyck.in/api/packages/create_order/?username=stuti@rekinza.com&key=9b12ab55-1e10-4d79-8a5a-67ca44c09db8";  


		$fields = array(
						"pickup_name" => "REKINZA-KOMAL",
						"pickup_phone" => "9871291261",
						"pickup_pincode" => "110001",
						"pickup_address" => "B1- Basement, Square One Mall, Saket District Center, Saket, New Delhi - 110017",
						"drop_name" => "$fullname",
						"drop_pincode" => "$zipcode",
						"drop_address" => "$fulladdress",
						"drop_phone" => "$mobile",
						"drop_country" => "INDIA",
						"pickup_time" => "$currdate",
						"reference_number" => "$pickup_id",
						"invoice_number" => "$pickup_id",
						"invoice_date" => "$invoice_date",
						"items" => "$order_items",
						"order_type" => "$ordermethodpyck",
						"invoice_value" => $items_value,
						"cod_value" => $collectible_amount
						);
						
						var_dump($fields); 
						
						
		$content = json_encode($fields);
		echo "<br>";

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
		array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$json_response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err     = curl_errno( $curl );
		$errmsg  = curl_error( $curl );
		$header  = curl_getinfo( $curl );

		curl_close($curl);
		$response = json_decode($json_response, true);
		$trackid = $response['waybill'];

		if ($trackid)
		{
			$query = "UPDATE thredshare_pickup SET return_tracking_id = '$trackid' WHERE id = '$pickup_id'";
			
			$result = mysql_query($query);

			if ($result == 'TRUE')
			{
				echo "Return invoice generated. Tracking ID is ".$trackid;
			}
			else
			{
				echo "ERROR: Return invoice generated, but Local DB not updated. Please set return track ID as ".$trackid." for pick up ID ".$pickup_id;
			}
				
		}
	}
	else
	{
		echo "Sorry! You are not authorised";
	}
}
else
{
	echo "Sorry ! You are not authorised";
}

	
?>