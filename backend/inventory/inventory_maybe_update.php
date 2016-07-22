<?php
include 'db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ( $numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "inventory panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
		{
		if(isset($_POST['sku_name']))
		{ //check if form was submitted

			include 'db_config.php';

			$sku_name = $_POST['sku_name'];
			$material = $_POST['material'];
			$measurements = $_POST['measurements'];
			$qc_status = $_POST['qc_status'];
			$size = $_POST['size'];
			$retail_value = $_POST['retail_value'];
			$suggested_price = $_POST['suggested_price'];
			$upload_status = $_POST['upload_status'];
			$maybe_reason = $_POST['maybe_reason'];
			
			$query = "UPDATE inventory SET qc_status ='$qc_status', material = '$material', measurements = '$measurements', size ='$size', retail_value = '$retail_value', suggested_price = '$suggested_price', upload_status = '$upload_status', maybe_reason = '$maybe_reason' WHERE sku_name = '$sku_name'  ";
			$result = mysql_query($query);
			
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
			
		$qc_status = 'maybe';
		$inventory_data_status = $_POST['inventory_data_status'];
		$sort_by = $_POST['sort_by'];

		if ($qc_status == 'maybe' && $inventory_data_status == 'Not complete')
		{

			$query = "SELECT * FROM inventory WHERE qc_status NOT IN ('accepted','rejected') AND ( material = '' OR measurements = '' OR size ='' OR retail_value='' OR suggested_price = '' OR upload_status ='') " ;
			
		}

		else if ($qc_status == 'maybe' && $inventory_data_status == 'Complete')
		{
			$query = "SELECT * FROM inventory WHERE qc_status NOT IN ('accepted','rejected') AND ( material != '' AND measurements != '' AND size !='' AND retail_value !='' AND suggested_price != '' AND upload_status !='' ) " ;
			
		}

		if($sort_by)
		{
			$query .= "ORDER BY ".$sort_by;
		}

		$result = mysql_query($query);
		$numresult = mysql_numrows($result);

		if ( $numresult > 0 )
		{
			
			?>
			<head>
				<script src="jquery-1.11.1.js"></script>
				<script src="FileSaver.js"></script>
			<head>
			
			<body>
			<div id ="inventory_table">
				<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
				
				<h1>Inventory Details</h1>
				
				<button id="btnExport" onclick="fnExcelReport();" > Export To Excel </button>
				
				<table id="report_table">
					<th>S. No</th>
					<th>SKU Code</th>
					<th>Customer Email ID</th>
					<th>Name</th>
					<th>
						<form action = "inventory_rejected_update.php" method = "POST">
							<input type = "text" value = 'Brand'  name = "sort_by" readonly ="true"> 
							<input type = "text" value = '<?php echo $inventory_data_status ?>' name = "inventory_data_status" hidden ="true">
							<input type = "Submit" value = "Sort!">
						</form>
					</th>
					<th>Color</th>
					<th>QC Status</th>
					<th>Condition </th>
					<th>Gently Used comments </th>
					<th>Material</th>
					<th>Measurements</th>
					<th>Size</th>
					<th>Maybe Reason</th>
					<th>Retail Value</th>
					<th>Suggested Price</th>
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
					$color= mysql_result($result,$i,'color');
					$qc_status = mysql_result($result,$i,'qc_status');
					$condition = mysql_result($result,$i,'condition');
					$gently_used_comments = mysql_result($result,$i,'gently_used_comments');
					$material = mysql_result($result,$i,'material');
					$measurements = mysql_result($result,$i,'measurements');
					$size = mysql_result($result,$i,'size');
					$maybe_reason = mysql_result($result,$i,'maybe_reason');
					$retail_value = mysql_result($result,$i,'retail_value');
					$suggested_price = mysql_result($result,$i,'suggested_price');
					$upload_status = mysql_result($result,$i,'upload_status');
				
				?>
				
				

				<tr>
					<td> <?php echo $i+1 ?></td>
					<form action = "inventory_maybe_update.php" method = "POST" target="_blank">
					<td><input type = "text" value = <?php echo $sku_name; ?> name = "sku_name" readonly = true>	</td>
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
					<td><textarea name = "maybe_reason"><?php echo $maybe_reason?></textarea></td>
					<td><input type = "number" value = <?php echo $retail_value ?> name = "retail_value"  onkeypress='validate(event)'>	</td>
					<td><input type = "number" value = <?php echo $suggested_price ?> name = "suggested_price"  onkeypress='validate(event)'>	</td>
					<td><input type = "text" value = '<?php echo $upload_status ?>' name = "upload_status"> </td>
					<!----------------------Dummy values so that on form submit, list with accepted and incomplete data items are loaded--------->
					
					<td hidden ="true"><input type = "text" value = '<?php echo $inventory_data_status ?>' name = "inventory_data_status"> </td>

					<td><input type = "Submit" value = "Update!"></td>
					</form>
				</tr>
					
					
			<?php
			$i++;
			}
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
		} //panelif ends
		else {
			echo "not allowed for this email id";
		}
} //numrows ends
else {
	echo "Sorry! Not authorized.";
}



?>
<style>

body
{
	background-color: #F9FFFB;
}

#seller_pickup_id
{
	width:1%;
	text-align:center;
	
}

h1
{
	background-color: #E3E0FA;
	text-align:center;
	width:40%;
	margin-left:auto;
	margin-right:auto;
	margin-bottom:2em;
	font-family: 'Century Schoolbook';
		

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
		font-family: 'Georgia';
		font-size:1.2em;
	}
	
td
	{
		text-align : center;
	}
	
	
tr:nth-child(odd)
	{
			background-color: #EAF1FB;
			font-size:1.1em;
	}

tr:nth-child(even)
	{
			background-color: #CEDEF4;
			font-size:1.1em;
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
		width:80%;
	
	
}
</style>

<script>

function fnExcelReport()
{
	var tab_text="<table><tr>";
    var textRange;
    tab = document.getElementById('report_table'); // id of actual table on your page

	console.log(tab.rows.length);
    for(j = 0 ; j < tab.rows.length ; j++) 
    {   
        tab_text=tab_text+tab.rows[j].innerHTML;
        tab_text=tab_text+"</tr><tr>";
    }

    tab_text = tab_text+"</tr></table>";

	var txt = new Blob([tab_text], {type: "text/plain;charset=utf-8"});
	saveAs(txt,"Export_Report.xls");
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
</script>