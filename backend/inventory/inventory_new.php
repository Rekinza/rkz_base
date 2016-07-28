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
$customer_email_id = $_GET['customer_email_id'];
$qc_owner = $_GET['qc_owner'];
?>
<html>
	<head>
		
		<script src="jquery-1.11.1.js"></script>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>	
	</head>
		<body>
			<h1>Inventory Panel</h1>
				<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			<!------------Input new inventory details here------------>
			
			<div id = "inventory_insert_form">
				
				<form action = "inventory_insert.php" method = "POST">
				<fieldset>
					<legend>Insert new inventory</legend>
					Pickup ID:
					<input type="number" name="pickup_id" id ="pickup_id" onkeypress ='validate(event)' value = '<?php echo $pickup_id; ?>' required></input>
					<br>
					Customer Email ID:
					<input type="text" name="customer_email_id" id ="customer_email_id" value = '<?php echo $customer_email_id; ?>' required></input>
					<br>
					Type:
					<?php

						include 'db_config.php';

						$get = mysql_query("SELECT entity_name FROM sku_code_mapping where type='type'");
						$option = '<option value="" disabled="disabled" selected="selected">Select Type</option>';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
						}
					?>
					<select name="type" id = "type" required>
						<?php echo $option ?>
					</select>
					<br>
					Sub-Type:
					<select name="sub_type" id ="sub_type" required>
					</select>
					<br>
					Category:
					<select name="category" id ="category">
					</select>
					<br>
					Sub-Category:
					<select name="sub_category" id ="sub_category">
					</select>
					<br>	
					<div>
					Extra-Topping:
					<ul>
					<?php
						$get = mysql_query("SELECT * FROM product_name_keywords WHERE 1 ORDER BY name ASC");
						
						while($row = mysql_fetch_assoc($get))
						{
							?>
							<li class= "et2"><input type = "checkbox" name = "extra_topping[]" class= "extra_topping" id = "extra_topping" value = '<?php echo $row['name']; ?>'><?php echo $row['name']; ?></li>
							<?php						
						}
						
						?>
						</ul>
					</div>
					<br>				
					<form action='' method='post'>
						<p><label>Brand:</label><input type='text' name='brand' id ='brand' value='' class='auto' required></input></p>
					</form>
					<br>
					<div class="color_div">
					Color:
					<?php

						$get = mysql_query("SELECT * FROM colors where 1");
						$option = '';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
						}
					?>
					<select name="color[]" id ="color" class="color" multiple required>
						<?php echo $option ?>
					</select>
					</div>
					<br>
					Product Name:
					<input type="text" name="product_name" id ="product_name" required></input>
					<br>
					Special Instr.:
					<input type="text" name="special_instr" id ="special_instr"></input>
					<br>
					<!-- for quantity, default value 1 -->
					Quantity:
					<input type="text" name="quantity" id="quantity" value="1" required></input>
					<br>
					-----------Fill below if QC done--------------
					<br>
					Quality Check Owner:
					<?php

						$get = mysql_query("SELECT * FROM qc_owner where 1");
						$option = '<option value = ""></option>';
						while($row = mysql_fetch_assoc($get))
						{
							if($qc_owner)  // If QC Owner info is present then have that as the default selection
							{							
								if($row['owner']==$qc_owner)
									$option .= '<option value = "'.$row['owner'].'" selected = "selected">'.$row['owner'].'</option>';
								else
									$option .= '<option value = "'.$row['owner'].'">'.$row['owner'].'</option>';
							}
							else
								$option .= '<option value = "'.$row['owner'].'">'.$row['owner'].'</option>';
						}
						
					?>

					<select name="qc_owner" id ="qc_owner" required>
						<?php echo $option ?>
					</select>	
					<br>
					Quality Check Status:
					<?php

						$get = mysql_query("SELECT * FROM qc_status where 1");
						$option = '<option value = ""></option>';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['status'].'">'.$row['status'].'</option>';
						}
					?>
					<select name="qc_status" id ="qc_status" onChange="changeTextBox();" required>
						<?php echo $option ?>
					</select>			
					</br>
					
					<div id = "rejection_div" hidden>
						<span style ="float:none; font-weight:bold">Rejection Reason</span><br>
						
						<?php
						$get = mysql_query("SELECT * FROM rejection_reason WHERE 1 ORDER BY name ASC");
						
						while($row = mysql_fetch_assoc($get))
						{
							?>
							<input type = "checkbox" name = "rejection_reason[]" id = "rejection_reason" value = '<?php echo $row['name']; ?>'><?php echo $row['name']; ?><br>
							<?php						
						}
						
						?>
						<span id ="rejection_reason_others_button" onclick ="rejection_others_click();" style ="float:none; background-color:#50c7c2;width:10%;font-weight:bold">Others</span>
						<input type = "textbox" name = "rejection_reason_others_textbox" id = "rejection_reason_others_textbox" placeholder ="Enter rejection reason" hidden></input>
						
						
					</div>
					
					
					<br>
					<input type = "text" name ="maybe_reason" id ="maybe_reason" placeholder ="Enter maybe reason" hidden>
					<br>
					Condition:
					
					<?php

						$get = mysql_query("SELECT * FROM `condition` WHERE 1");
						$option = '<option value="" disabled="disabled" selected="selected">Select Condition</option>';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
						}
					?>
					<select name="condition" id = "condition" onchange ="changeConditionTextBox();" hidden>
					<?php echo $option ?>
					</select>
					</br>
					<div id = "new_with_tag_prices" hidden>
						<br>
							<input type="number" name="retail_value" id ="retail_value" value ="" placeholder = "Retail Value" onkeypress ='validate(event)' onchange = "FillSuggPrice(this.value);">
						<br>
							<input type="number" name="suggested_price" id ="suggested_price" value ="" placeholder = "Suggested Price" onkeypress ='validate(event)' >
					</div>
					<br>
					
					
					<input type = "text" name ="gently_used_comments" id ="gently_used_comments" placeholder ="Gently Used Comments" hidden>
					</br>

					<div id = "input_size_div" hidden>
							<?php
							$get = mysql_query("SELECT type FROM size where 1");
							
								$option = '<option value="" disabled="disabled" selected="selected">Select Size</option>';
							
							while($row = mysql_fetch_assoc($get))
							{
								$option .= '<option value = "'.$row['type'].'">'.$row['type'].'</option>';
							}
							?>
							<select name ="size">
								<?php echo $option ?>
							</select>
					</div>
					
					<div id ="input_material" hidden>
						<!--td><textarea name = "material" id = "material" style ="height:100px;width:250px"></textarea></td-->
						<div class = "material">
						<b>Primary Material</b><br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Cotton" onchange = "enable_disable_options('primary','Cotton');">Cotton<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Polyester" onchange = "enable_disable_options('primary','Polyester');">Polyester<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Nylon" onchange = "enable_disable_options('primary','Nylon');">Nylon<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Viscose" onchange = "enable_disable_options('primary','Viscose');">Viscose<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Rabbit Hair" onchange = "enable_disable_options('primary','Rabbit Hair');">Rabbit Hair<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Elastane" onchange = "enable_disable_options('primary','Elastane');">Elastane<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Silk" onchange = "enable_disable_options('primary','Silk');">Silk<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Leather" onchange = "enable_disable_options('primary','Leather');">Leather<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Acryllic" onchange = "enable_disable_options('primary','Acryllic');">Acryllic<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Lycra" onchange = "enable_disable_options('primary','Lycra');">Lycra<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Rayon" onchange = "enable_disable_options('primary','Rayon');">Rayon<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Cashmere" onchange = "enable_disable_options('primary','Cashmere');">Cashmere<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Polyamide" onchange = "enable_disable_options('primary','Polyamide');">Polyamide<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Denim" onchange = "enable_disable_options('primary','Denim');">Denim<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Linen" onchange = "enable_disable_options('primary','Linen');">Linen<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Velour" onchange = "enable_disable_options('primary','Velour');">Velour<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Suede" onchange = "enable_disable_options('primary','Suede');">Suede<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Modal" onchange = "enable_disable_options('primary','Modal');">Modal<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Wool" onchange = "enable_disable_options('primary','Wool');">Wool<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Metal" onchange = "enable_disable_options('primary','Metal');">Metal<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Cloth" onchange = "enable_disable_options('primary','Cloth');">Cloth<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Net" onchange = "enable_disable_options('primary','Net');">Net<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Chiffon" onchange = "enable_disable_options('primary','Chiffon');">Chiffon<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Georgette" onchange = "enable_disable_options('primary','Georgette');">Georgette<br>
						</div>
						
						<div class = "material">
						<b>Secondary Material</b><br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Cotton" onchange = "enable_disable_options('secondary','Cotton');">Cotton<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Polyester" onchange = "enable_disable_options('secondary','Polyester');">Polyester<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Nylon" onchange = "enable_disable_options('secondary','Nylon');">Nylon<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Viscose" onchange = "enable_disable_options('secondary','Viscose');">Viscose<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Rabbit Hair" onchange = "enable_disable_options('secondary','Rabbit Hair');">Rabbit Hair<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Elastane" onchange = "enable_disable_options('secondary','Elastane');">Elastane<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Silk" onchange = "enable_disable_options('secondary','Silk');">Silk<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Leather" onchange = "enable_disable_options('secondary','Leather');">Leather<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Acryllic" onchange = "enable_disable_options('secondary','Acryllic');">Acryllic<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Lycra" onchange = "enable_disable_options('secondary','Lycra');">Lycra<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Rayon" onchange = "enable_disable_options('secondary','Rayon');">Rayon<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Cashmere" onchange = "enable_disable_options('secondary','Cashmere');">Cashmere<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Polyamide" onchange = "enable_disable_options('secondary','Polyamide');">Polyamide<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Denim" onchange = "enable_disable_options('secondary','Denim');">Denim<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Linen" onchange = "enable_disable_options('secondary','Linen');">Linen<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Velour" onchange = "enable_disable_options('secondary','Velour');">Velour<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Suede" onchange = "enable_disable_options('secondary','Suede');">Suede<br>
							<input type="checkbox" class ="secondary_material" name="secondary_material[]" id = "secondary_material"value="Modal" onchange = "enable_disable_options('secondary','Modal');">Modal<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Wool" onchange = "enable_disable_options('primary','Wool');">Wool<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Metal" onchange = "enable_disable_options('primary','Metal');">Metal<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Cloth" onchange = "enable_disable_options('primary','Cloth');">Cloth<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Net" onchange = "enable_disable_options('primary','Net');">Net<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Chiffon" onchange = "enable_disable_options('primary','Chiffon');">Chiffon<br>
							<input type="checkbox" class ="primary_material" name="primary_material[]" id = "primary_material"value="Georgette" onchange = "enable_disable_options('primary','Georgette');">Georgette<br>

						</div>	
			
					</div>
					
					
					
					<p class = "submit">
						<input type = "submit" value = "Insert">
					</p>
					</fieldset>
				</form>
				
			</div>
		</body>

	</html>
	<?php
	} //panelif ends
	else {
		echo "not allowed for this email id";
	}
} //numrows if ends
else {
	echo "Sorry! Not authorized.";
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

.material
{
	display:inline-block;
	margin-left:40px;
	margin-top:12px;
	border-style:groove;
	padding:12px;
}

.extra_topping
{
   display:inline-block;
   
}

.et2
{
   float: left;
   width: 50%;
    list-style-type: none;
}

#product_name
{
	width: 400px;
}

.ui-autocomplete {
    position: absolute;
    z-index: 1000;
    cursor: default;
    padding: 0;
    margin-top: 2px;
    list-style: none;
    background-color: #ffffff;
    border: 1px solid #ccc
    -webkit-border-radius: 5px;
       -moz-border-radius: 5px;
            border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
       -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
}
.ui-autocomplete > li {
  padding: 3px 20px;
}
.ui-autocomplete > li.ui-state-focus {
  background-color: #DDD;
}
.ui-helper-hidden-accessible {
  display: none;
}


</style>

<script>

/*************Fill brand using autofill******************/
$(function() {
    
    //autocomplete
    $(".auto").autocomplete({
        source: "inventory_search_brand.php",
        minLength: 1
    });                

});

/*************Fill sub-type based on type on change event******************/

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
					update_categories_from_subtype();
					generate_product_name();
				}
				});
			
			}
		);		
	}
);

/*************Fill category based on sub_type on change event******************/
$(document).ready(
	function()
	{
		$('#sub_type').change(
			function()
			{
				update_categories_from_subtype();	
				generate_product_name();		
			}
		);		
	}
);

/*************Fill category based on sub_type on change event******************/
$(document).ready(
	function()
	{
		$('#category').change(
			function()
			{
				update_sub_categories_from_categories();	
				generate_product_name();		
			}
		);		
	}
);
/*************Generate Product Name*****************************************/

$(document).ready(
	function()
	{
		$('#brand').change(
			function()
			{
				generate_product_name();			
			}
		);	
		$('#sub_type').change(
			function()
			{
				generate_product_name();			
			}
		);
	}
);


/**********************************LIMIT options ************************************************************/
$(document).ready(
	function()
	{
		$(".color").on("click", "option", function () 
		{
			if($(this).hasClass("selected"))
			{
				if($(this).siblings(":selected").length < 4 && document.getElementById("multicolor"))
				{
					$(this).removeClass("selected");
					var remove_element = document.getElementById("multicolor");
	 		  		remove_element.remove();

				}
				else
				{
					$(this).removeClass("selected");
				}

			} 
			else
			{
				$(this).addClass("selected");
				if ( 3 == $(this).siblings(":selected").length ) 
		    	{
		        	$(this).parents('.color_div').append('<div id="multicolor"> Multicolor </div>');
	 		  	}
			}
	    	generate_product_name();
		});

		$(".extra_topping").on('change', function(evt) {
   			if($(this).parent('.et2').siblings('.et2').children(':checked').length >= 2) 
   			{
       			this.checked = false;
   			}
   			generate_product_name();
		});
	}
);

String.prototype.capitalize = function() {
    
	if(this.indexOf(' ') >= 0)
    {
		   var space_index = this.indexOf(' ');
		   var tail = this.slice(space_index);
		   var capital_tail = tail.charAt(1).toUpperCase() + tail.slice(2);

		   var head = this.slice(0,space_index);
		   var capital_head = head.charAt(0).toUpperCase() + head.slice(1);
		   var complete = capital_head +" "+ capital_tail;
		   return complete;
		   
    }
	else
	{
		return this.charAt(0).toUpperCase() + this.slice(1);
	}
	
	
}

function generate_product_name()
{
	var color = document.getElementById("color");
	var attributes = document.getElementById("extra_topping");
	var category = document.getElementById("category").value;
	var subtype = document.getElementById("sub_type").value;
	var subcat = document.getElementById("sub_category").value;
	var brand = document.getElementById("brand").value;
	var name = "";
	var addendum = "";

	if(subtype != "suit" && ($("#type :selected").val() == "Clothing"))
	{
		if((color.value).length && (attributes.value).length && brand.length && subtype.length)
		{
			if( category.length> 0 && subcat.length == 0)
			{
				addendum = category;
			}

			else if(subcat.length > 0)
			{
				addendum = subcat + " "+category;
			}
			
			var attr = $( ".extra_topping:checkbox:checked" ).map(function() {return this.value;});
			var colors = [];
			if(document.getElementById('multicolor') && ($("#color :selected").length > 3))
			{
				colors.push('Multicolored');
			}
			else
			{
			$("#color :selected").each(function(){colors.push($(this).val()); });
			//console.log(colors);
			}
			if(colors.length == 1 && attr.length == 1)
			{
				name = colors[0].capitalize()+" "+attr[0].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 2 && attr.length == 1)
			{
				name = colors[0]+" & "+colors[1].capitalize()+" "+attr[0].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 3 && attr.length == 1)
			{
				name = colors[0].capitalize()+", "+colors[1].capitalize()+" & "+colors[2].capitalize()+" "+attr[0].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 1 && attr.length == 2)
			{
				name = colors[0].capitalize()+" "+attr[0].capitalize()+" & "+attr[1].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 1 && attr.length == 3)
			{
				name = colors[0].capitalize()+" "+attr[0].capitalize()+", "+attr[1].capitalize()+" & "+attr[2].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 2 && attr.length == 2)
			{
				name = colors[0].capitalize()+" & "+colors[1].capitalize()+" "+attr[0].capitalize()+" & "+attr[1].capitalize()+" "+addendum.capitalize();
			}
			if(colors.length == 3 && attr.length == 2)
			{
				name = colors[0].capitalize()+", "+colors[1].capitalize()+" & "+colors[2].capitalize()+" "+attr[0].capitalize()+" & "+attr[1].capitalize()+" "+addendum.capitalize();
			}


		}
		if(name.length > 50)
		{
			var truncated_name = name.substring(0, 46);
			name = truncated_name+"..."

		}
	}
	document.getElementById("product_name").value = name;
}

/*************Fill category based on sub-type whenever subtype list is updated******************/

function update_categories_from_subtype()
{
	$.ajax({
	type:'POST',
	url:'inventory_sub_type_from_type.php',
	data:
	{
		'sub_type':$('#sub_type').val()
	},
	async:false,
	success:function(message)
	{
		$('#category').html(message);
		update_sub_categories_from_categories();
	}
	});
}

/*************Fill subcategories based on category whenever categories list is updated******************/

function update_sub_categories_from_categories()
{
	$.ajax({
	type:'POST',
	url:'inventory_sub_type_from_type.php',
	data:
	{
		'category':$('#category').val()
	},
	async:false,

	success:function(message)
	{
		$('#sub_category').html(message);
	}
	});
}


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


/********************* Display rejection reason box only if QC status selected is rejected*********************/
function changeTextBox() {
    var comp = document.getElementById('qc_status');
    if(comp.value=='rejected')
	{   document.getElementById('rejection_div').hidden=false;
		document.getElementById('maybe_reason').hidden=true;
		document.getElementById('gently_used').hidden=true;
		document.getElementById('input_size_div').hidden=true;
		document.getElementById('input_material').hidden=true;
	}
    else if (comp.value =='maybe')
	{       document.getElementById('rejection_div').hidden=true;
			document.getElementById('maybe_reason').hidden=false;
			document.getElementById('gently_used').hidden=true;
			document.getElementById('input_size_div').hidden=true;
			document.getElementById('input_material').hidden=true;
	}
    else
	{       document.getElementById('rejection_div').hidden=true;
			document.getElementById('maybe_reason').hidden=true;
			document.getElementById('condition').hidden=false;
			document.getElementById('input_size_div').hidden=false;
			document.getElementById('input_material').hidden=false;
	}

	}

/********************* Display Gently Used box only if condition is gently used*********************/
function changeConditionTextBox() {
    var comp = document.getElementById('condition');
    if(comp.value=='gently used')
	{   
		document.getElementById('gently_used_comments').hidden=false;
		document.getElementById('new_with_tag_prices').hidden=true;
	}

    else
	{
		document.getElementById('gently_used_comments').hidden=true;
		if(comp.value=='new with tag')
		{
			document.getElementById('new_with_tag_prices').hidden=false;			
		}
		else
		{
			document.getElementById('new_with_tag_prices').hidden=true;
		}
	}

}

/************************** Parent function to control display of secondary material options based on selection of primary material and vice versa *****/
function enable_disable_options(material_type,material)
{
	
	var option_type = material_type;
	
	var option = material;
	
	checked_flag = GetCheckboxByValue(option_type,option);
	
	HideCheckboxByValue(option_type,option,checked_flag);
	
	return;	
}

/**************************Function to check whether primary/secondary material option clicked on checked or unchecked *****/

function GetCheckboxByValue(option_type,option) 
{
		if (option_type == 'primary')
        {
			var inputs = document.getElementsByClassName('primary_material');
		}
		else if (option_type == 'secondary')
        {
			var inputs = document.getElementsByClassName('secondary_material');
		}
		console.log(inputs.length);
		
        for (var i = 0; i < inputs.length; i++) {
                if(inputs[i].type == "checkbox" && inputs[i].value == option) 
				{
           			if (inputs[i].checked == true)
						{
							return 1;
						}
						else
						{
							return 0;
						}
                }
        }
        return;
}

/**************************Function to hide/unhide the secondary material options based on selection of primary material and vice versa *****/

function HideCheckboxByValue(option_type,option,checked_flag) 
{
		if (option_type == 'primary')
        {
			var inputs = document.getElementsByClassName('secondary_material');
		}
		else if (option_type == 'secondary')
        {
			var inputs = document.getElementsByClassName('primary_material');
		}
		
        for (var i = 0; i < inputs.length; i++) {
				
                if(inputs[i].type == "checkbox" && inputs[i].value == option) 
				{
					
					if(checked_flag == 1)
						inputs[i].hidden = true;
					else
						inputs[i].hidden = false;						
                }

        }
		return;
}

function rejection_others_click()
{
	 document.getElementById('rejection_reason_others_textbox').hidden=false;
}


$(window).bind("pageshow", function() 
{
  
  document.getElementById('maybe_reason').value='';
  document.getElementById('retail_value').value='';
  document.getElementById('suggested_price').value='';
  document.getElementById('gently_used_comments').value='';
});

function FillSuggPrice(price) 
{
	
    var condition = document.getElementById('condition').value;
    var brand = document.getElementById('brand').value;
	if(condition == "new with tag")
	{

		$.ajax({
		type: 'POST',
		url: 'inventory_suggested_pricing.php',
		data: 
		{
			'brand' : brand,
			'condition' : condition,
			'price' : price
		},
		async: false,
		success:function(message) {
			var json_data = JSON.parse(message);
			var price_id = 'suggested_price';

			document.getElementById('suggested_price').value = json_data.special_price;
			
		}
	});
	
	} //if ends
     
}

$('input[type="submit"]').on('click', function() {
 // skipping validation part mentioned above
 //alert("head");
 var atLeastOneExtraFeaturesIsChecked = $('.extra_topping:checkbox:checked').length;
 var atLeastOneMaterialIsChecked = $('.primary_material:checkbox:checked').length;
 if(0)//atLeastOneExtraFeaturesIsChecked == 0)
 {
         alert("Please fill the extra features");
         event.preventDefault();
 }
 else if(0)//atLeastOneMaterialIsChecked == 0)
 {
         alert("Please fill the material(s)");
         event.preventDefault();
 }
});

</script>