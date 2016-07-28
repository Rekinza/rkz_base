<?php
include 'db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
$email = mysql_result($result,0,'email');
$access_level = mysql_result($result,0,'access_level');
if ($numresult > 0)
{
	$blockname = "inventory panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	if(isset($_POST['sku_name']))
	{ //check if form was submitted

		include 'db_config.php';

		$sku_name = $_POST['sku_name'];
		$rejection_reason = $_POST['rejection_reason'];
		$rejection_status = $_POST['rejection_status'];
		$special = $_POST['special'];
		$qc_status = $_POST['qc_status'];
		
		$query = "UPDATE inventory SET qc_status =  '$qc_status', rejection_reason = '$rejection_reason', rejection_status = '$rejection_status', special = '$special' WHERE sku_name = '$sku_name'";
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
		
	$qc_status = 'rejected';
	$inventory_data_status = $_POST['inventory_data_status'];
	$sort_by = $_POST['sort_by'];

	if ($qc_status == 'rejected' && $inventory_data_status == 'Not complete')
	{
		
		$query = "SELECT * FROM inventory WHERE qc_status = 'rejected' AND ( rejection_reason = '' OR rejection_status = '')" ;
		
	}

	else if ($qc_status == 'rejected' && $inventory_data_status == 'Complete')
	{
		$query = "SELECT * FROM inventory WHERE qc_status = 'rejected' AND (rejection_reason != '' AND rejection_status != '' )" ;
		
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
			<h1>Inventory Details</h1>
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
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
				<th>Special</th>
				<th>Rejection Reason</th>
				<th>Rejection Status</th>
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
				$qc_status= mysql_result($result,$i,'qc_status');
				$condition = mysql_result($result,$i,'condition');
				$gently_used_comments = mysql_result($result,$i,'gently_used_comments');	
				$special= mysql_result($result,$i,'special');
				$rejection_reason = mysql_result($result,$i,'rejection_reason');
				$rejection_status = mysql_result($result,$i,'rejection_status');
			
			?>
			
			

			<tr>
				<td> <?php echo $i+1 ?></td>
				<form action = "inventory_rejected_update.php" method = "POST" target="_blank">
				<td><input type = "text" value =<?php echo $sku_name; ?> name = "sku_name" readonly = true>	</td>
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
			
				<td><textarea name = "special"><?php echo $special?></textarea></td>
				<td><textarea name = "rejection_reason"><?php echo $rejection_reason?></textarea></td>
	 			<td><textarea name ="rejection_status"><?php echo $rejection_status?></textarea></td>
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
	saveAs(txt,"Recruitment_Report.xls");
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