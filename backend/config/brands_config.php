<?php
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';
include 'db_config.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "control panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	if(isset($_POST['brand']))
		{ //check if form was submitted
			
			
			require_once '../../app/Mage.php';
			Mage::app();

			$brand = $_POST['brand'];
			$code = $_POST['code'];
			
			$brand = trim($brand);
			$code = trim($code);
			$code = strtoupper($code);   // In case code is in lower case

			$query = "SELECT entity_name FROM sku_code_mapping WHERE entity_name ='$brand'";
			$result = mysql_query($query);
			$numresult = mysql_numrows($result);
			
			if ($numresult > 0)
			{
				echo 'Brand already exists in sku_code_mapping';
			}
			
			else
			{
				$query = "SELECT entity_name FROM sku_code_mapping WHERE code ='$code'";
				$result = mysql_query($query);
				$numresult = mysql_numrows($result);
				if ($numresult > 0)
				{
					echo 'Code already exists in sku_code_mapping';
				}
				else
				{
					
					/* Search for brand in magento */
					$optionValue = getAttributeOptionValue("brands", $brand);
					
					if ($optionValue == 'true')
					{
						echo "Brand exists in magento but not in sku_code_mapping";
					}
					
					else
					{
						
						$query = "INSERT into sku_code_mapping values (NULL,'$brand','brand','','$code')"; 
						$result = mysql_query($query);
						if ($result == TRUE)
						{
							$arg_attribute = 'brands';
							$arg_value = $brand;
						
							try {

							$attr_model = Mage::getModel('catalog/resource_eav_attribute');
							$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
							$attr_id = $attr->getAttributeId();

							$option['attribute_id'] = $attr_id;
							$option['value']['any_option_name'][0] = $arg_value;

							$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
							$setup->addAttributeOption($option);
						
							echo "Brand ".$brand." with code ".$code." added successfully";
						
							}
						
							catch (Exception $e) {
							
								echo "Brand addition failed in magento but added in sku_code_mapping. Please add manually in magento";
						
							}
						}
						else
						{
							echo "Record addition failed";
						}
					}			
				
				}
			}

			mysql_close();
			
		}

	?>

	<html>

		<head></head>
		<h1>Brand Configuration</h1>

		<div id ="inventory_panels">
		<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
				<fieldset>
					<form action = "brands_config.php" method = "POST">
					<h3>Add New Brand</h3>			
					<input type = "text" name = "brand" placeholder ="Enter brand name" required >
						<input type = "text" name = "code" placeholder = "Enter brand code" required>
						<p class = "submit">
							<input type = "submit" value = "Submit">
						</p>			
					</form>
					<br>
					<a href = "..\backend_home.php"><button class ="panel_button">Home</button></a>

				</fieldset>
				
			</div>	

	</html>
	<?php
	 } //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //numrows ends
else 
{
	echo "Sorry! Not authorized";
} 

		
/* Function to search for attribute option in an attribute type */
	function getAttributeOptionValue($arg_attribute, $arg_value) 
	{ 
		$attribute_model = Mage::getModel('eav/entity_attribute'); 
		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;   
		$attribute_code = $attribute_model->getIdByCode('catalog_product', $arg_attribute); 
		$attribute = $attribute_model->load($attribute_code);   
		$attribute_table = $attribute_options_model->setAttribute($attribute); 
		$options = $attribute_options_model->getAllOptions(false);   
		foreach($options as $option) 
		{ 
			if ($option['label'] == $arg_value) 
			{ 
				return true; 
			} 
		}   
		return false; 
	}

	 
?>

<style>
body
{
	background-color: #F0F0F0  ;
}

h1 
{
	text-align: center;
	color: #bb1515;
	font-family: 'Arial';
	background-color: #b7c3c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
}

span
{
	float:left;
	display:inline-block;
	width:49%;
}

h2
{
	text-align: center;
	color: #bb1515;
	font-family: 'Arial';
	background-color: #b7c3c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
}


div
{
	margin-left:auto;
	margin-right:auto;
	width:50%;
	text-align:center;
	line-height:2em;
}

legend
{
	font-size: 1.2em;
	background-color :#669999;
	color : white;
	width:100%;

}

.panel_button
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 

a
{
	text-decoration:none;
}

a:link 
{
	color: aquamarine;
	
}

a:visited
{
	color:white;
}

a:hover
{
	color:grey;
}


</style>