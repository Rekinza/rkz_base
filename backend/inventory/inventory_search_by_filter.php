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
	$sku_name = $_GET['sku_name'];

	$query = "SELECT * from inventory WHERE sku_name = '".$sku_name."' ";

	$result = mysql_query($query);

	$numresult = mysql_numrows($result);

	if ($numresult == 0 )
	{
		echo "SKU not found";
	}
	else
	{
			$i = 0;

			while ( $i < $numresult)                  
			{
			$customer_email_id = mysql_result($result,$i,'customer_email_id');
			$type= mysql_result($result,$i,'type');
			$sub_type = mysql_result($result,$i,'sub_type');
			$category= mysql_result($result,$i,'category');
			$sub_category = mysql_result($result,$i,'sub_category');
			$brand = mysql_result($result,$i,'brand');
			$color = mysql_result($result,$i,'color');
			$pickup_id = mysql_result($result,$i,'pickup_id');
			$product_name = mysql_result($result,$i,'product_name');
			$special = mysql_result($result,$i,'special');
			$qc_owner = mysql_result($result,$i,'qc_owner');
			$qc_status = mysql_result($result,$i,'qc_status');
			$condition = mysql_result($result,$i,'condition');
			$gently_used_comments = mysql_result($result,$i,'gently_used_comments');
			$material = mysql_result($result,$i,'material');
			$measurements = mysql_result($result,$i,'measurements');
			$size = mysql_result($result,$i,'size');
			$retail_value = mysql_result($result,$i,'retail_value');
			$suggested_price = mysql_result($result,$i,'suggested_price');
			$upload_status = mysql_result($result,$i,'upload_status');
			$rejection_reason = mysql_result($result,$i,'rejection_reason');
			$rejection_status = mysql_result($result,$i,'rejection_status');
			$maybe_reason = mysql_result($result,$i,'maybe_reason');
			$quantity = mysql_result($result,$i,'quantity');
			$i++;	
			}
			
			?>
		<html>
			<head><script src="jquery-1.11.1.js"></script></head>
			<body>
				
				<h1>Search by SKU</h1>
				<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
			<form action = "inventory_update_sku.php" method = "POST"  enctype="multipart/form-data">
					<fieldset>
						<legend>Inventory Details</legend>
								SKU Name : <input type = "text" name = "sku_name" value = '<?php echo $sku_name ?>'  readOnly="true"> <br>
								Pickup ID : <input type = "text" name = "pickup_id" value = '<?php echo $pickup_id ?>'> <br>
								Customer Email ID : <input type = "text" name = "customer_email_id" value = '<?php echo $customer_email_id ?>'> <br>
								Product Name : <input type = "text" name = "product_name" value = '<?php echo $product_name ?>'> <br>
								Type:
								<?php
										$get = mysql_query("SELECT entity_name FROM sku_code_mapping where type='type'");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['entity_name'] != $type)	
												$option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
											else
													$option .= '<option value = "'.$row['entity_name'].'" selected = "selected">'.$row['entity_name'].'</option>';
										}
									?>
								<select name="type" id = "type" >
									<?php echo $option ?>
								</select>
								<br>
						
								Sub Type : 
								<?php
										$get = mysql_query("SELECT entity_name FROM sku_code_mapping where type='sub-type'");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['entity_name'] != $sub_type)	
												$option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
											else
													$option .= '<option value = "'.$row['entity_name'].'" selected = "selected">'.$row['entity_name'].'</option>';
										}
									?>
								<select name="sub_type" id = "sub_type" >
									<?php echo $option ?>
								</select>
								<br>
								Category : <input type = "text" name = "category" value = '<?php echo $category ?>' > <br>
								Sub Category : <input type = "text" name = "sub_category" value = '<?php echo $sub_category ?>' > <br>
								Brand : 
								<?php
										$get = mysql_query("SELECT entity_name FROM sku_code_mapping where type='brand'");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['entity_name'] != $brand)	
												$option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
											else
													$option .= '<option value = "'.$row['entity_name'].'" selected = "selected">'.$row['entity_name'].'</option>';
										}
									?>
								<select name="brand" id = "brand" >
									<?php echo $option ?>
								</select>
								Color : 
								<?php
										$get = mysql_query("SELECT * FROM colors WHERE 1");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if( stristr($color,$row['type'],FALSE) == FALSE)   //$row['type'] != $color)	
												$option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
											else
											{
													$option .= '<option value = "'.$row['type'].'" selected = "selected">'.$row['type'].'</option>';
											}
										}
										
									?>
								<select name="color[]" id = "color" multiple>
									<?php echo $option ?>
								</select>
								<br>
								Quantity:
									<input type="text" name="quantity" id="quantity" value='<?php echo $quantity ?>'  required>
								<br>
								Special : <textarea name = "special"><?php echo $special?></textarea><br>
								QC Owner:
								<?php
										$get = mysql_query("SELECT owner FROM qc_owner where 1");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['owner'] != $qc_owner)	
												$option .= '<option value = "'.$row['owner'].'">'.$row['owner'].'</option>';
											else
													$option .= '<option value = "'.$row['owner'].'" selected = "selected">'.$row['owner'].'</option>';
										}
									?>
								<select name="qc_owner" id = "qc_owner">
									<?php echo $option ?>
								</select>
								<br>
						
								QC Status:
								<?php
										$get = mysql_query("SELECT status FROM qc_status where 1");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['status'] != $qc_status)	
												$option .= '<option value = "'.$row['status'].'">'.$row['status'].'</option>';
											else
													$option .= '<option value = "'.$row['status'].'" selected = "selected">'.$row['status'].'</option>';
										}
									?>
								<select name="qc_status" id = "qc_status" onChange="changeTextBox();">
									<?php echo $option ?>
								</select>
								<br>
								Condition:
								<?php
										$get = mysql_query("SELECT * FROM `condition` where 1");
										$option = '';
										
										while($row = mysql_fetch_assoc($get))
										{
											if($row['type'] != $condition)	
												$option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
											else
													$option .= '<option value = "'.$row['type'].'" selected = "selected">'.$row['type'].'</option>';
										}
									?>
							
								
								<select name="condition" id = "condition" onchange ="changeConditionTextBox();">
										<?php echo $option ?>
								</select>
								<br>
								Gently Used Comments : <input type = "text" name = "gently_used_comments" value = '<?php echo $gently_used_comments ?>'> <br>
								Material:<textarea name = "material"><?php echo $material?></textarea><br>
								Measurements:<textarea name ="measurements"><?php echo $measurements?></textarea><br>
								
								Size:
								<?php
									$get = mysql_query("SELECT type FROM size where 1");
									if( !$size )
										$option = '<option value="" disabled="disabled" selected="selected">Select Size</option>';
									else
										$option = '<option value="" disabled="disabled">Select Size</option>';
									
									while($row = mysql_fetch_assoc($get))
									{
										if($row['type'] != $size)	
											$option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
										else
												$option .= '<option value = "'.$row['type'].'" selected = "selected">'.$row['type'].'</option>';
									}
								?>
								<select name ="size">
									<?php echo $option ?>
								</select>
							
								Retail Value :<input type = "number" value = <?php echo $retail_value ?> name = "retail_value"  onkeypress='validate(event)'><br>
								Suggested Price:<input type = "number" value = <?php echo $suggested_price ?> name = "suggested_price"  onkeypress='validate(event)'><br>
								Upload Status :<input type = "text" value = '<?php echo $upload_status ?>' name = "upload_status"><br>
								Rejection Reason :
								<textarea name = "rejection_reason"><?php echo $rejection_reason?></textarea><br>
								Rejection Status :
								<textarea name = "rejection_status"><?php echo $rejection_status?></textarea><br>
								Maybe Reason :
								<textarea name = "maybe_reason"><?php echo $maybe_reason?></textarea><br>
								
							</fieldset>
							<p class = "submit">	
								<input type = "Submit" value = "Update!">
							</p>
				</form>
			</body>
			<?php	
			
	}
	mysql_close();
} //panelif ends
	else {
		echo "not allowed for this email id";
}
} //snumrows if ends
else {
	echo "Sorry! Not authorized.";
}?>
</html>
<style>

body
{
	background-color: #F9EEFF;
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

form
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

.submit input
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 


<style>

<script>
/*************Fill sub-type based on type******************/

$(document).ready(
	function()
	{
		$('#type').change(
			function()
			{
				$.ajax({
				type:'POST',
				url:'inventory_sub_type_from_type.php',
				data:
				{
					'type':$('#type').val()
				},
			
				success:function(message)
				{
					$('#sub_type').html(message);
				}
				});
			
			}
		);		
	}
);

/*************Fill customer_email_id based on pickup_id******************/

$(document).ready(
	function()
	{
		$('#pickup_id').change(
			function()
			{
				$.ajax({
				type:'POST',
				url:'inventory_cust_email_id_from_pickup_id.php',
				data:
				{
					'pickup_id':$('#pickup_id').val()
				},
			
				success:function(message)
				{
					$('#customer_email_id').val(message);
				}
				});
			
			}
		);		
	}
);



/********************* Validate numeric entry in form field*********************/
function validate(evt) 
{
	var theEvent = evt || window.event;
	var key = theEvent.keyCode || theEvent.which;
	key = String.fromCharCode( key );
	var regex = /[0-9]|\./;
	if( !regex.test(key) ) 
	{
		theEvent.returnValue = false;
		if(theEvent.preventDefault) theEvent.preventDefault();
	}
}

/********************* Display gently used comments box only if condition selected is gently used*********************/
function changeTextBox() {
    var comp = document.getElementById('qc_status');
    if(comp.value=='accepted')
	{   
		document.getElementById('condition').hidden=false;
	}

    else
			document.getElementById('condition').hidden=true;
	}

}
/********************* Display Gently Used box only if condition is gently used*********************/
function changeConditionTextBox() {
    var comp = document.getElementById('condition');
    if(comp.value=='gently used')
	{   
		document.getElementById('gently_used_comments').hidden=false;
	}

    else
	{
		document.getElementById('gently_used_comments').hidden=true;
	}

}

	
</script>

</script>