<?php

include 'db_config.php';

require_once '../../app/Mage.php';
Mage::app();


$sub_type = $_GET['sub_type'];

$min_id = $_GET['min_id'];

if ($sub_type == 'Tops')
{
	$check_level = 'sub_category';
}
else
{
	$check_level = 'category';
}

if(!$min_id)
{
	$query = "SELECT * FROM `inventory` WHERE sub_type = '$sub_type' AND qc_status = 'accepted' AND $check_level = '' LIMIT 40";
}
else
{
	
	$query = "SELECT * FROM `inventory` WHERE sub_type = '$sub_type' AND qc_status = 'accepted' AND $check_level = '' AND id > '$min_id' LIMIT 40";
}

echo $query;

$result = mysql_query($query);

$numresult = mysql_numrows($result);

if($numresult >0)
{

$i = 0;
?>
<body>
<head>
	<script src="jquery-1.11.1.js"></script>
</head>
<table>
<th>ID</th>
<th>SKU</th>
<th>Name</th>
<th>Category</th>
<th>Category List</th>
<th>Sub Cat</th>
<th>Sub Cat List</th>
<th>Image</th>
<?php
while ($i <$numresult)
{
	$product_id = mysql_result($result,$i,'id');
	$sku = mysql_result($result,$i,'sku_name');
	$name = mysql_result($result,$i,'product_name');
	$category = mysql_result($result,$i,'category');
	$sub_category = mysql_result($result,$i,'sub_category');
	
	$product = Mage::getModel('catalog/product');
	$id = Mage::getModel('catalog/product')->getResource()->getIdBySku($sku);
	if ($id)
	{
		$product->load($id);

		$imgurl = $product->getImageUrl();
		
		$quantity = $product->getStockItem()->getQty();
				
		if ($quantity <1)
		{
			$i++;
			continue;
		}
			
	}
	else
	{
		$imgurl = "Blank";
	}
	
	?>
	<tr>
		<form action = "inventory_set_category_update.php" method = "POST" target="_blank">
			<td><input type = "text" name = "product_id" id = "product_id" value = <?php echo $product_id ; ?> readonly = true></td>
			<td><input type = "text" name = "sku" id = "sku" value = <?php echo $sku ; ?> readonly = true></td>
			<td><?php echo $name ; ?></td>
			<td><?php echo $category ; ?></td>
			<td>	<?php
					$q = "SELECT entity_name from sku_code_mapping where type ='category' AND parent = '$sub_type'";
					
					$res = mysql_query($q);
					
					$option = '<option value="" disabled="disabled" selected="selected">Select Category</option>';
					
					while($row = mysql_fetch_assoc($res))
					{
						if($row['entity_name'] != $category)	
							$option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
						else
							$option .= '<option value = "'.$row['entity_name'].'" selected = "selected">'.$row['entity_name'].'</option>';
					}
					
				
				?>
				<select name ="category" id ="category<?php echo $i;?>" onchange = "UpdateSubCatList(this.value,<?php echo $i; ?>);">
						<?php echo $option ?>
				</select>
			</td>

			<td><?php echo $sub_category ; ?></td>
				<?php
					$q = "SELECT entity_name from sku_code_mapping where type ='sub-category' AND parent = '$category'";
					
					$res = mysql_query($q);
					
					$option = '<option value="" disabled="disabled" selected="selected">Select Sub Category</option>';
					
					while($row = mysql_fetch_assoc($res))
					{
						if($row['entity_name'] != $sub_category)	
							$option .= '<option value = "'.$row['entity_name'].'">'.$row['entity_name'].'</option>';
						else
							$option .= '<option value = "'.$row['entity_name'].'" selected = "selected">'.$row['entity_name'].'</option>';
					}
					
				
				?>
			<td>
				<select name ="sub_category" id = "sub_category<?php echo $i;?>">
						<?php echo $option ?>
				</select>		
			</td>
			<td><?php if ($imgurl !="Blank")
				{
				?>
					<img src = "<?php echo $imgurl;?> "></img>
				<?php
				}
				else
				{
				?>
					<span><strong> Not present in Magento </strong></span>
				<?php
				}
				?>
			</td>
			<td><input type = "Submit" value = "Update!"></td>
		</form>
	</tr>
	<?php
	$i++;
}

$min_id = $product_id;
}
else
{
	echo "No results";
}

?>
</table>

<a href = "inventory_set_category.php?sub_type=<?php echo $sub_type?>&&min_id=<?php echo $min_id;?>"><button id ="labels_button">Next</button></a>
</body>


<style>
input#labels_button
{
	cursor:pointer; /*forces the cursor to change to a hand when the button is hovered*/
	padding:5px 25px; /*add some padding to the inside of the button*/
	background:rgb(202, 60, 60);; /*the colour of the button*/
	border:1px solid #33842a; /*required or the default border for the browser will appear*/
	/*give the button curved corners, alter the size as required*/
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	/*give the button a drop shadow*/
	-webkit-box-shadow: 0 0 4px rgba(0,0,0, .75);
	-moz-box-shadow: 0 0 4px rgba(0,0,0, .75);
	box-shadow: 0 0 4px rgba(0,0,0, .75);
	/*style the text*/
	color:#f3f3f3;
	font-size: 14 px;
	margin : auto;
	display:block;
	width:8%;
}

input#labels_button:hover, input#gobutton:focus
{
	background-color :#399630; /*make the background a little darker*/
	/*reduce the drop shadow size to give a pushed button effect*/
	-webkit-box-shadow: 0 0 1px rgba(0,0,0, .75);
	-moz-box-shadow: 0 0 1px rgba(0,0,0, .75);
	box-shadow: 0 0 1px rgba(0,0,0, .75);
}
</style>

<script>
/*************Fill sub-type based on type******************/


function UpdateSubCatList(category, index)
{

	$.ajax({
	type:'POST',
	url:'inventory_sub_type_from_type.php',
	data:
	{
		'category':category
	},

	success:function(message)
	{
		$('#sub_category'+index).html(message);
	}
	});

}

</script>