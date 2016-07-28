<?php

if(isset($_POST['sku_name']))
{ //check if form was submitted

	include 'db_config.php';

	$sku_name = $_POST['sku_name'];
	$qc_status = $_POST['qc_status'];
	$material = $_POST['material'];
	$measurements = $_POST['measurements'];
	$size = $_POST['size'];
	$special = $_POST['special'];
	$retail_value = $_POST['retail_value'];
	$suggested_price = $_POST['suggested_price'];
	$upload_status = $_POST['upload_status'];
	
	$query = "UPDATE `inventory` SET qc_status =  '$qc_status', material = '$material', measurements = '$measurements', size ='$size', retail_value = '$retail_value', suggested_price = '$suggested_price', upload_status = '$upload_status', special = '$special' WHERE sku_name = '$sku_name'  ";
	$result = mysql_query($query);
	
	echo mysql_error();
	
	if ($result == 'TRUE')
	{
		echo 'Record Updated Successfully';
	}
	else
	{
		echo 'Record Update Failed';
	}
	

	mysql_close();

}


include 'db_config.php';

$creation_start_date = $_POST['creation_start_date'];
$creation_end_date = $_POST['creation_end_date'];
$qc_status = $_POST['status'];

/* Format our dates */
$creation_start_date = date('Y-m-d H:i:s', strtotime($creation_start_date));
$creation_end_date = date('Y-m-d H:i:s', strtotime($creation_end_date));

/* Set end date time till 23 hrs, 59 min and 59 seconds i.e. EOD */
$creation_end_date = strtotime ( '+23 hours' , strtotime ($creation_end_date )) ;
$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);

$creation_end_date = strtotime ( '+59 minutes' , strtotime ($creation_end_date ));
$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);

$creation_end_date = strtotime ( '+59 seconds' , strtotime ($creation_end_date ));
$creation_end_date = date('Y-m-d H:i:s', $creation_end_date);


echo $creation_start_date."<br>";
echo $creation_end_date."<br><br>";

if($qc_status == 'all') 
{
	$query = "SELECT * FROM inventory WHERE creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' ORDER BY brand ASC";
	$query_grouped = "SELECT qc_owner, SUM(quantity) as total FROM inventory WHERE creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' GROUP BY qc_owner";
}
else
{
	$query = "SELECT * FROM inventory WHERE creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' AND qc_status = '".$qc_status."'  ORDER BY brand ASC";
	$query_grouped = "SELECT qc_owner, SUM(quantity) as total FROM inventory WHERE creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' AND qc_status = '".$qc_status."' GROUP BY qc_owner";	
}

$result = mysql_query($query);
//$result_set = mysqli_fetch_all($result,MYSQLI_ASSOC);

$numresult = mysql_numrows($result);

$result_group_wise = mysql_query($query_grouped);
//$result_set2 = mysqli_fetch_all($result_group_wise,MYSQLI_ASSOC);

$numresult_grouped = mysql_numrows($result_group_wise);

if($numresult_grouped >0)
{
	?>
	<table>
	<th>QC Owner</th>
	<th>Total Processed</th>
	<th>Details</th>
	
	<?php
	$j = 0;
	while ( $j < $numresult_grouped )
	{
		
		//$result_set = mysqli_fetch_all($result_group_wise,MYSQLI_ASSOC);
		$qc_owner = mysql_result($result_group_wise,$j,'qc_owner');
		$qc_count = mysql_result($result_group_wise,$j,'total');
		
		?>
		<tr>
			<td><?php echo $qc_owner?></td>
			<td><?php echo $qc_count?></td>
		
		<?php
		$query_at_qc_owner_level = "SELECT qc_status as status,SUM(quantity) as qc_status_total from inventory where creation_date BETWEEN '$creation_start_date' AND '$creation_end_date' AND qc_owner = '".$qc_owner."' GROUP by qc_status";

		$result_at_qc_owner_level = mysql_query($query_at_qc_owner_level);
		//$result_at_qc_owner_level = mysqli_fetch_all($result3,MYSQLI_ASSOC);

		$numresult_at_qc_owner_level = mysql_numrows($result_at_qc_owner_level);
		
				
		$k = 0;
		
		?>
		<td style = "text-align:left">
		<?php
		
		while ($k < $numresult_at_qc_owner_level)
		{
			$status = mysql_result($result_at_qc_owner_level,$k,'status');
			$qc_status_count = mysql_result($result_at_qc_owner_level,$k,'qc_status_total');
			?>
				<b><?php echo $status." " ?></b><?php echo $qc_status_count?>
			
			<?php
			$k++;
		} 
		?>
		</td>
		</tr>
		<?php
		
/* 		 	while ($row = mysql_fetch_array($result_at_qc_owner_level, MYSQL_ASSOC)) {
		print_r($row);
	}  */	

		echo "<br>";
		
		$j++;
	}
	?>
	
	</table>
	<?php
 	
}

if ( $numresult > 0 )
{
	
	?>
	<head>
		<script src="jquery-1.11.1.js"></script>
		<script src="FileSaver.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js"></script>
	
	<head>
	
	<body>
	<div id ="inventory_table">
		<h1>Inventory Details</h1>
				
		<form action = "inventory_print_labels.php" method = "POST" target="_blank">
			<input type = "text" name ="start_date" value = '<?php echo $creation_start_date ; ?>' hidden = "true" ></input>
			<input type = "text" name ="end_date" value= '<?php echo $creation_end_date ; ?>'  hidden = "true"></input>
			<input id = "labels_button" type="Submit" value = "Print Labels"></input>
		</form>
		
		<table id="report_table">
			<th>S. No</th>
			<th>SKU Code</th>
			<th>Cust. Email ID</th>
			<th>Name</th>
			<th>Brand			</th>
			<th>Color</th>
			<th>QC Status </th>
			<th>Condition </th>
			<th>Gently Used comments </th>
			<th>Material</th>
			<th>Measurements</th>
			<th>Size</th>
			<th>Special</th>
			<th>Retail Value</th>
			<th>Suggested Price</th>
			<th>Photo Name</th>
			<th>Upload Status</th>
			<th hidden = "true">QC Status</th>
			<th hidden = "true">Inventory Data Status</th>
	<?php
	$i = 0;
	while ( $i < $numresult )
	{
			$sku_name = mysql_result($result,$i,'sku_name');
			$customer_email_id = mysql_result($result,$i,'customer_email_id');
			$product_name= mysql_result($result,$i,'product_name');
			$brand = mysql_result($result,$i,'brand');
			$qc_status = mysql_result($result,$i,'qc_status');
			$color= mysql_result($result,$i,'color');
			$material = mysql_result($result,$i,'material');
			$measurements = mysql_result($result,$i,'measurements');
			$condition = mysql_result($result,$i,'condition');
			$gently_used_comments = mysql_result($result,$i,'gently_used_comments');
			$size = mysql_result($result,$i,'size');
			$special = mysql_result($result,$i,'special');
			$retail_value = mysql_result($result,$i,'retail_value');
			$suggested_price = mysql_result($result,$i,'suggested_price');
			$upload_status = mysql_result($result,$i,'upload_status');
			
			$product_name_trim = trim($product_name);
			$photo_title = $brand." ".$product_name_trim;
			$photo_title = str_replace(" ", "-", $photo_title);
			$photo_title = strtolower($photo_title);

		?>
		
		

		<tr id="<?php echo $i+1 ?>">
			<td> <?php echo $i+1 ?></td>
			<form action = "inventory_search_by_creation_date.php" method = "POST" target="_blank">
			<td><textarea name="sku_name" style ="width:50px;" readonly = "true"><?php echo $sku_name; ?></textarea></td>
			<td><?php echo $customer_email_id ;?></td>
			<td ><?php echo $product_name;?></td>
			<td><?php echo $brand; ?></td>
			<td><?php echo $color; ?></td>
			<td>
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
				<select name ="qc_status">
					<?php echo $option ?>
				</select>
			<td><?php echo $condition; ?></td>
			<td><?php echo $gently_used_comments; ?></td>			
			<td><textarea name = "material"><?php echo $material?></textarea></td>
 			<td><textarea name ="measurements"><?php echo $measurements?></textarea></td>
			<td>
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
			</td>
			<td><textarea name = "special"><?php echo $special?></textarea></td>
			<td><input type = "number" value = <?php echo $retail_value ?> name= "retail_value" id = "retail_value<?php echo $i+1 ?>" onkeypress='validate(event)' onchange = "FillSuggPrice(('<?php echo $brand ?>') , '<?php echo $condition ?>' , this.value, '<?php echo $i + 1?>');">	
			
				 <button id ="myBtn" type = "button" value="getprice" onclick="getprice('<?php echo $i +1?>' , '<?php echo $sku_name ?>', '<?php echo $brand ?>','<?php echo $condition ?>')"> GET PRICE </button>
			</td>
			<td><input type = "number" value = <?php echo $suggested_price ?> name = "suggested_price" id ="suggested_price<?php echo $i+1?>" onkeypress='validate(event)'>	<span> </span></td> 
			<td><?php echo $photo_title?></td>
			<td><input type = "text" value = '<?php echo $upload_status ?>' name = "upload_status"> </td>
			<!--td>
				<form action='' method='post'>
					<input type='text' name='upload_status' id ='upload_status' value='' class='auto'>
				</form>
			</td-->
			
			<!--Dummy values so that on form submit, list with accepted and incomplete data items are loaded-->
			
			<td hidden ="true"><input type = "text" value = '<?php echo $inventory_data_status ?>' name = "inventory_data_status"> </td>
			
			<td><input type = "Submit" value = "Update!"></td>
			</form>
		</tr>
			
			
	<?php
	$i++;
	}
	mysql_close($con);
?>
	</table>
	</div>
	</body>

<?php
}
else
{
	echo "No results found";

}

//mysqli_close($con);
?>
<style>

body
{
	background-color: #F9FFFB;
	font-family: 'Avenir';
}

#seller_pickup_id
{
	width:1%;
	text-align:center;
	
}

.abc
{
	display: inline-block;
	width:100%;
}

.abc2
{
	width: 100%;
}
.item-box{
	width: 20%;
    padding: 10px;
    display: inline-block;
    font-size: 12px;
    border: 1px solid #eaeaea;
}
h1
{
	background-color: #E3E0FA;
	text-align:center;
	width:40%;
	margin-left:auto;
	margin-right:auto;
	margin-bottom:2em;
	font-family: 'Avenir';
		

}

table
	{
		margin-left: auto;
		margin-right: auto;
		font-color: #d3d3d3;
		background-color: #ADD8E6;
	
	}
	
th
	{
		background-color: #C0C0C0;
		font-family: 'Avenir';
		font-size:1.2em;
	}
	
td
	{
		text-align : center;
	}
	
	
tr:nth-child(odd)
	{
			background-color: #EAF1FB;
			font-size:1em;
	}

tr:nth-child(even)
	{
			background-color: #CEDEF4;
			font-size:1em;
	}

tr.highlight   
	{    
		background-color: #063774;   
		color: White;   
	}  

textarea
	{
		height:120px;
	}
	
#btnExport
	{
		display:block;
		margin-left: auto;
		margin-right: auto;
		margin-bottom:2em;
		height: 35px;
		color: white;
		border-radius: 10px;
		text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
		background: rgb(202, 60, 60); /* this is maroon */
	}	

input
{
		width:100%;
	
	
}

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
.modal {
    display: block; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: #efefef; /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
#close {
    color: #ffffff;
    float: right;
    font-size: 20px;
    font-weight: bold;
    background: #50c7c2;
    position: absolute;
    text-transform: uppercase;
    padding: 8px;
}

#close:hover,
#close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

</style>

<script>

/*************Fill upload status using autofill******************/
$(function() {
    
    //autocomplete
    $(".auto").autocomplete({
        source: "inventory_autofill_upload_status.php",
        minLength: 1
    });                

});

function fnExcelReport()
{
	var tab_text="<table><tr>";
    var textRange;
    tab = document.getElementById('report_table'); // id of actual table on your page

	//console.log(tab.rows.length);
    for(j = 0 ; j < tab.rows.length ; j++) 
    {   
        tab_text=tab_text+tab.rows[j].innerHTML;
        tab_text=tab_text+"</tr><tr>";
    }

    tab_text = tab_text+"</tr></table>";

	var txt = new Blob([tab_text], {type: "text/plain;charset=utf-8"});
	saveAs(txt,"Inventory_Accepted_Report.xls");
}

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

/********************* Suggested price autofill based on retail value*********************/
function FillSuggPrice(brand, condition, price, index) 
{
	
    var retail_price = document.getElementById('retail_value');
	

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
			var price_id = 'suggested_price'+index;

			document.getElementById('suggested_price'+index).value = json_data.special_price;

			var abc = $('[id="'+price_id+'"]').parent().children("span").html(json_data.off+"% off");
			
		}
	});
    
      
}

function getprice(index , sku, brand, condition) {
	//var sku = sku;
	//var bigdiv = document.createElement("tr");


	$.ajax({
		type: 'POST',
		url: 'inventory_retail_pricing.php',
		data: 
		{
			'sku' : sku
		},
		async: false,
		success:function(message) 
		{
			if(isJson(message))
			{	
				console.log("jSon found");

				var json_data1 = JSON.parse(message);
				var count = json_data1.count;
				
				var bigdiv = document.createElement("span");
				bigdiv.className = "modal";
				bigdiv.id = "price-modal";
				var modalinside = document.createElement("div");
				modalinside.className = "modal-content";
				var modalbody = document.createElement("div");
				modalbody.className = "modal-body";
				var t = document.createTextNode("Close");
				var closeBtn = document.createElement("p");
				closeBtn.id = "close";
				closeBtn.appendChild(t);
				for(i = 0; i<count; i++)
				{
					
					var newspan = document.createElement("div");
					newspan.className = "item-box";

					var element00 = document.createElement("input");
					element00.type = "checkbox";
					element00.name = "p_check[]";
					element00.className = "abc1";
					element00.value = json_data1.prices[i];
					element00.onchange = function () { putprice(index, this.value, brand, condition); };

					newspan.appendChild(element00);

					var element0 = document.createElement("span");
					element0.name = "p_name[]";
					element0.innerHTML = json_data1.productnames[i];

					newspan.appendChild(element0);

					var element1 = document.createElement("span");
					//element1.type = "text";
	            	element1.name = "p_price[]";
	            	element1.className = "abc";
	            	element1.innerHTML = json_data1.prices[i];
	            	
	            	newspan.appendChild(element1);

	            	var element2 = document.createElement("span");
					//element2.type = "text";
	            	element2.name = "p_brand[]";
	            	element2.className = "p_brand";
	            	element2.innerHTML = json_data1.brands[i]; 

	            	newspan.appendChild(element2); 
					
	            	var element3 = document.createElement("img");
	            	element3.className = "abc";
	            	element3.src = json_data1.images[i];                  

	            	newspan.appendChild(element3);
	            	modalbody.appendChild(newspan);
	            	modalbody.appendChild(closeBtn);
	            	modalinside.appendChild(modalbody);
	            	bigdiv.appendChild(modalinside);
					
				}
			
				$(bigdiv).insertAfter('[id="'+index+'"]');
				
				//modal code
				// Get the modal
			
				var modal = document.getElementById('price-modal');

				// Get the button that opens the modal
			
				var btn = document.getElementById("myBtn");

			
				var span = document.getElementById("close");

				// When the user clicks on the button, open the modal 
			
				span.onclick = function() {
					modal.remove();
				}
				// }

				// When the user clicks anywhere outside of the modal, close it
			
				window.onclick = function(event) {
				    if (event.target == modal) {
				        modal.remove();
				    }
				}

			} //ifjson ends
			else
			{
				alert (message);
			}



		} //success ends
	}); 

}

function putprice(id, price, brand, condition)
{
  price = parseInt(price); //Remove trailing zeros in decimals
  var domprice = document.getElementById("retail_value"+id);
  domprice.value = price;
  var closemodal = document.getElementById("price-modal");
  closemodal.remove();

  console.log(brand+condition+price+id);
  FillSuggPrice(brand, condition, price, id);

}

function isJson(str)
{
	try
	{
		JSON.parse(str);
	}
	catch(e)
	{
		return false;
	}
	return true;
} 



</script>