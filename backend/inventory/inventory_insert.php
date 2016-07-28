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

		$pickup_id =$_POST['pickup_id'];
		$product_name=$_POST['product_name'];
		$cust_email_id=$_POST['customer_email_id'];
		$type=$_POST['type'];
		$sub_type=$_POST['sub_type'];
		$category=$_POST['category'];
		$sub_category=$_POST['sub_category'];
		$brand =$_POST['brand'];
		$color =$_POST['color'];
		$condition =$_POST['condition'];
		$gently_used_comments =$_POST['gently_used_comments'];
		$special_instr =$_POST['special_instr'];
		$qc_owner=$_POST['qc_owner'];
		$qc_status=$_POST['qc_status'];
		$rejection_reason =$_POST['rejection_reason'];
		$rejection_reason_others_textbox =$_POST['rejection_reason_others_textbox'];
		$maybe_reason =$_POST['maybe_reason'];
		$retail_value =$_POST['retail_value'];
		$suggested_price =$_POST['suggested_price'];
		$quantity = $_POST['quantity'];
		$size = $_POST['size'];
		$primary_material = $_POST['primary_material'];
		$secondary_material = $_POST['secondary_material'];

		/******Validate Brand entry************/
		$query = "SELECT * FROM sku_code_mapping WHERE entity_name = '".$brand."' and type = 'brand'";

		$result = mysql_query($query);

		$numresult = mysql_numrows($result);

		if ($numresult < 1)
		{
			echo "Invalid brand name ".$brand." Please re-enter inventory details";
		}

		else
		{

			/***************Get product type ********************/

			$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$type."' ";
			$result = mysql_query($query); 

			$type_code = mysql_result($result,0,'code');


			/***************Get product sub-type ********************/

			$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$sub_type."' ";
			$result = mysql_query($query); 

			$sub_type_code = mysql_result($result,0,'code');

			/***************Get product brand ********************/

			$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$brand."' ";
			$result = mysql_query($query); 

			$brand_code = mysql_result($result,0,'code');


			$sku_code = $type_code.'-'.$sub_type_code.'-'.$brand_code ;



			/****************Search last added item with same type, sub type and brand combination******************/

			$query = "SELECT sku_name from inventory WHERE sku_name LIKE '".$sku_code."%' ";

			$result = mysql_query($query); 

			$numresult = mysql_numrows($result);

			if ($numresult == 0 )
			{
				$sku_code = $sku_code.'-01';
			}

			else
			{	
				$j = 0;
				$max_index = 0;

				while ($j < $numresult)
				{
					$sku_name = mysql_result($result,$j,'sku_name');
					$sku_name_array = explode("-",$sku_name);
					
					$sku_index = $sku_name_array[3];		
					if ($sku_index >$max_index)
					{
						$max_index = $sku_index;
					}
					$j++;

				}
				$max_index = $max_index + 1;

				if ($max_index <10)
				{	
					$sku_code = $sku_code.'-0'.$max_index;
				}
				else
				{	
					$sku_code = $sku_code.'-'.$max_index;
				}
			}

			echo $sku_code."<br>";

			/**************Convert color array into a single string*******************/

			for($i=0;$i<count($color) - 1;$i++){
				
				$color_string .= $color[$i];
				$color_string .= ",";
			}

			$color_string .= $color[$i];


			/**************Convert primary material array into a single string*******************/

			if(count($primary_material) > 0)
			{
				$primary_material_string = "Primary Material: ";

				for($i=0;$i<count($primary_material) - 1;$i++){
					
					$primary_material_string .= $primary_material[$i];
					$primary_material_string .= ", ";
				}

				$primary_material_string .= $primary_material[$i];
			}


			/**************Convert secondary material array into a single string*******************/

			if(count($secondary_material) > 0)
			{
				$secondary_material_string = "Secondary Material: ";

				for($i=0;$i<count($secondary_material) - 1;$i++){
					
					$secondary_material_string .= $secondary_material[$i];
					$secondary_material_string .= ", ";
				}

				$secondary_material_string .= $secondary_material[$i];
			}
			
			if( ($primary_material != NULL ) && ($secondary_material != NULL))
			{
				$material = $primary_material_string."\r\n".$secondary_material_string;
			}
			else if( $primary_material != NULL  )
			{
				$material = $primary_material_string;
			}
			else if( $secondary_material != NULL )
			{
				$material = $secondary_material_string;
			}
			
			$material = mysql_real_escape_string($material);
			
			echo $material;
			
			
			/***************Rejection Reason**************************/
			if(count($rejection_reason) > 0)
			{
				$rejection_reason_string = "";

				for($i=0;$i<count($rejection_reason) - 1;$i++){
					
					$rejection_reason_string .= $rejection_reason[$i];
					$rejection_reason_string .= ", ";
				}

				$rejection_reason_string .= $rejection_reason[$i];
			}
			
			// Add rejection reason text box entry to rejection reason string
			if($rejection_reason_others_textbox)
			{
				
				if($rejection_reason_string)
				{
					$rejection_reason_string .=",";
				}
				$rejection_reason_string .=$rejection_reason_others_textbox;
				
				$rejection_reason_string = mysql_real_escape_string($rejection_reason_string);
				
				//Add new rejection reason to table
				
				$rejection_reason_others_textbox = mysql_real_escape_string($rejection_reason_others_textbox);
				
				$query = "INSERT INTO `rejection_reason` VALUES ('','$rejection_reason_others_textbox')";
				
				$result = mysql_query($query);
				
				if ($result == 'TRUE')
				{
					echo 'New Rejection reason inserted successfully<br>';
				}
				else
				{
					echo 'New Rejection reason insertion failed<br>';
				}
				
			
			}
			/*****Clean any special characters in the gently used comment *****/
			$gently_used_comments = mysql_real_escape_string($gently_used_comments);
			/******************************************************************/
			
			
			$timestamp = date("Y-m-d H:i:s");
				
			$query = "INSERT INTO `inventory` VALUES ('','$sku_code','$cust_email_id','$type','$sub_type','$category','$sub_category','$brand','$color_string','$condition','$gently_used_comments','$pickup_id','$product_name','$special_instr','$qc_owner','$qc_status','$material','','$size','$suggested_price','$retail_value','','$timestamp','','$quantity','$rejection_reason_string','','$maybe_reason')";
			
			$result = mysql_query($query);

			echo mysql_error();

			if ($result == 'TRUE')
			{
				echo 'Record inserted successfully';
			}
			else
			{
				echo 'Record insertion failed';
			}
		}

		?>

		<html>
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			<br><br>
					<a href = '<?php echo "inventory_new.php?pickup_id=".$pickup_id."&customer_email_id=".$cust_email_id."&qc_owner=".$qc_owner;?>'><button class ="panel_button">New Inventory Prefill Basic Info</button></a>			
			<br><br>
					<a href = "inventory_new.php"><button class ="panel_button">Insert New Inventory</button></a>
		</html>

		<?php

		mysql_close();
	} //panelif ends
	else 
	{
		echo "Sorry! You are not authorised";
	}
}//numrows ends
else 
{
	echo "Sorry! You are not authorized"; 
}

?>

<style>
.panel_button
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 
</style>