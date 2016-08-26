<?php
include 'db_config.php';
	include '../../app/Mage.php';
	Mage::init();
// include_once("../../login-with-google-using-php/config.php");
// include_once("../../login-with-google-using-php/includes/functions.php");
// include '../utils/access_block.php';

// $email_logged_in = $_SESSION['google_data']['email'];
// $query = "SELECT * from user_access where email LIKE '$email_logged_in'";
// $result = mysql_query($query);
//$numresult =  mysql_numrows($result);
if (1)//$numresult > 0)
{
	// $email = mysql_result($result,0,'email');
	// $access_level = mysql_result($result,0,'access_level');
	// $blockname = "inventory panel";
	// $panel_access = ac_level($blockname);
	if(1)//$access_level <= $panel_access)
	{

		$sku_name =$_POST['sku_name'];
		$pickup_id =$_POST['pickup_id'];
		$product_name=$_POST['product_name'];
		$cust_email_id=$_POST['customer_email_id'];
		$type=$_POST['type'];
		$sub_type=$_POST['sub_type'];
		$category=$_POST['category'];
		$sub_category=$_POST['sub_category'];
		$brand =$_POST['brand'];
		$color =$_POST['color'];
		$quantity =$_POST['quantity'];
		$special_instr =$_POST['special_instr'];
		$qc_owner=$_POST['qc_owner'];
		$qc_status=$_POST['qc_status'];
		$condition=$_POST['condition'];
		$gently_used_comments=$_POST['gently_used_comments'];
		$material=$_POST['material'];
		$measurements=$_POST['measurements'];
		$size=$_POST['size'];
		$retail_value=$_POST['retail_value'];
		$suggested_price=$_POST['suggested_price'];
		$upload_status=$_POST['upload_status'];
		$suggested_price=$_POST['suggested_price'];
		$rejection_reason =$_POST['rejection_reason'];
		$rejection_status =$_POST['rejection_status'];
		$maybe_reason =$_POST['maybe_reason'];
			
		$sku_name = strtoupper($sku_name);   /* In case sku passed is in lower case*/
		
		$new_sku_code = get_sku_code($type,$sub_type,$brand);
		
		$new_sku_code_extract = split_sku($new_sku_code,'-',3);
		
		$old_sku_code_extract = split_sku($sku_name,'-',3);
		
	//	echo $old_sku_code_extract."<br>";
		
	//	echo $new_sku_code_extract."<br>";
		$str_comp = strcmp($old_sku_code_extract,$new_sku_code_extract);
		
		if ($str_comp ==0)
		{
			$sku_code_final = $sku_name;		
		}
		else
		{
			$sku_code_final = $new_sku_code;
		}
		
		echo "<strong> Please note</strong> Updated SKU Code: ".$sku_code_final."<br><br>";
		
		/**************Convert color array into a single string*******************/

		for($i=0;$i<count($color) - 1;$i++){
			
			$color_string .= $color[$i];
			$color_string .= ",";
		}

		$color_string .= $color[$i];

		
		$query = "UPDATE inventory SET sku_name = '$sku_code_final', color = '$color_string', quantity = '$quantity', qc_owner = '$qc_owner' ,qc_status ='$qc_status', `condition` ='$condition', gently_used_comments = '$gently_used_comments',material = '$material', measurements = '$measurements', size ='$size', retail_value = '$retail_value', suggested_price = '$suggested_price', upload_status = '$upload_status', maybe_reason = '$maybe_reason', pickup_id = '$pickup_id', customer_email_id = '$cust_email_id', product_name = '$product_name', type = '$type', sub_type = '$sub_type', category = '$category', sub_category = '$sub_category', brand ='$brand', special = '$special_instr', rejection_reason = '$rejection_reason', rejection_status = '$rejection_status'  WHERE sku_name = '$sku_name' ";
		
		$result = mysql_query($query);

	echo mysql_error();

	if ($result == 'TRUE')
	{
		//start magento querying
		$product = Mage::getModel('catalog/product');
		$id = Mage::getModel('catalog/product')->getResource()->getIdBySku($sku_code_final);
		if ($id) 
		{
			$product->load($id);

			//logic for msrp	
			if($suggested_price >0 && $suggested_price < 750)
			{
				$msrp = $suggested_price - 200;
			}
			elseif ($suggested_price <= 5000) 
			{
				$msrp = 0.7*$suggested_price;
			}
			elseif ($suggested_price <= 50000)
			{
				$msrp = 0.8*$suggested_price;	
			}
			else
			{
				$msrp = 0.85*$suggested_price;
			}

			$product->setMsrp($msrp)
		      ->setPrice($retail_value)
	    	  ->setSpecialPrice($suggested_price);

			 try 
			 {
			    $product->save();
			        echo "Saved and Updated on Magento."."\r\n";
			  }
			  catch (Exception $ex) {
			        echo "<pre>".$ex."</pre>";
			  }
		}
		else
		{
			echo "Not updated on magento because id was not found.";
		}
		echo 'Record updated successfully';
	}
	else
	{
		echo 'Record update failed';
	}
	mysql_close();


	/********************************Utility Functions*************************


	function get_sku_code($type,$sub_type,$brand)
	{
			/***************Get product type *******************

		$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$type."' ";
		$result = mysql_query($query); 

		$type_code = mysql_result($result,0,'code');


		/***************Get product sub-type *******************

		$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$sub_type."' ";
		$result = mysql_query($query); 

		$sub_type_code = mysql_result($result,0,'code');

		/***************Get product brand *******************

		$query = "SELECT code from sku_code_mapping WHERE entity_name = '".$brand."' ";
		$result = mysql_query($query); 

		$brand_code = mysql_result($result,0,'code');


		$sku_code = $type_code.'-'.$sub_type_code.'-'.$brand_code ;



		/****************Search last added item with same type, sub type and brand combination*****************

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
		return $sku_code;
			
		
	}

	function split_sku($string,$delim,$nth){
		$max = strlen($string);
		$n = 0;
		for($i=0;$i<$max;$i++){
			if($string[$i]==$delim){
				$n++;
				if($n>=$nth){
					break;
				}
			}
		}
		$arr = substr($string,0,$i);
		return $arr;
	} */


	?>

	<html>
		<br><br>
				<a href = "../backend_home.php"><button class ="panel_button">Home</button></a>
				<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
		<br><br>

	</html>
	<?php
	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //numrows ends
else {
	echo "Sorry! Not authorized.";
}

function get_sku_code($type,$sub_type,$brand)
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
		return $sku_code;
			
		
	}

	function split_sku($string,$delim,$nth){
		$max = strlen($string);
		$n = 0;
		for($i=0;$i<$max;$i++){
			if($string[$i]==$delim){
				$n++;
				if($n>=$nth){
					break;
				}
			}
		}
		$arr = substr($string,0,$i);
		return $arr;
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

<script>

