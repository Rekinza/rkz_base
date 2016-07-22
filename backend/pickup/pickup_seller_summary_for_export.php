<?php

include '../db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
$email = mysql_result($result,0,'email');
$access_level = mysql_result($result,0,'access_level');
if ( $numresult > 0)
{
		$blockname = "pickup panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access)
	{	
	$customer_email_id = $_GET['customer_email_id'];

$start_date = $_GET['pickup_start_date'];
$end_date = $_GET['pickup_end_date'];
$waybill_number = $_GET['waybill_number'];
$status_search = $_GET['status_search'];
$return_dispatch_start_date = $_GET['return_dispatch_start_date'];
$return_dispatch_end_date = $_GET['return_dispatch_end_date'];

if ($customer_email_id != NULL)
{
	$query = "SELECT * FROM thredshare_pickup WHERE email = '$customer_email_id'" ;
	$result = mysql_query($query);

	$numresult = mysql_numrows($result);
	
	if ($numresult == 0)
		{
		/************* customer email id to be taken from customer entity********************/
		$query = "SELECT entity_id from customer_entity WHERE email = '$customer_email_id'";

		
		$result = mysql_query($query);
		
		$customer_id = mysql_result($result,0,'entity_id');
		
		
		
		$query = "SELECT * FROM thredshare_pickup WHERE customer_id = '$customer_id'" ;
		$result = mysql_query($query);
	}
}

else if ($start_date != NULL && $end_date != NULL)
{
	
	$query = "SELECT * FROM thredshare_pickup WHERE pick_up_date BETWEEN '$start_date' AND '$end_date' ";
	$result = mysql_query($query);
}

else if ($waybill_number != NULL)
{
	
	$query = "SELECT * FROM thredshare_pickup WHERE waybill_number = '$waybill_number' ";
	$result = mysql_query($query);
}

else if ($status_search != NULL)
{
	$query = "SELECT * FROM thredshare_pickup WHERE status = '$status_search' ";
	$result = mysql_query($query);
}

else if ($return_dispatch_start_date != NULL && $return_dispatch_end_date != NULL)
{
	
	$query = "SELECT * FROM thredshare_pickup WHERE return_dispatch_date BETWEEN '$return_dispatch_start_date' AND '$return_dispatch_end_date' ";
	$result = mysql_query($query);
}

$numresult = mysql_numrows($result);

if ( $numresult > 0 )
{
	
	?>
	<head>
		<script src="jquery-1.11.1.js"></script>
		<script src="FileSaver.js"></script>
	<head>
	
	<body>
	<div id ="pickup_date_table">
		<h1>Pickup Details</h1>
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
		<button id="btnExport" onclick="fnExcelReport();" > Export To Excel </button>
		
		<table id="report_table">
			<th>S. No</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email ID</th>
			<th>City</th>
			<th>State</th>
			<th>Address</th>
			<th>Pin-Code</th>
			<th>Mobile</th>
			<th>Pick Up Date</th>
			<!--th>Start Time</th>
			<th>End Time</th-->
			<th>Pick Up ID</th>
			<th style ="width:10px">Status</th>
			<!--th>Pickup Cost</th-->
			<th>Logistics Partner</th>
			<th>Waybill</th>
			<th>Items</th>
			<th>Item Count</th>
			<th>Approx Value</th>
			<th> Unaccepted Item Status</th>
			<th>Unaccepted Action</th>
			<th>No. Of Item</th>
			<th>Accepted Count</th>
			<th>Rejected Count</th>
			<th>Inv</th>
			<th>Send Email</th>
	<?php
	$i = 0;
	while ( $i < $numresult )
	{
			$first_name = mysql_result($result,$i,'first_name');
			$last_name = mysql_result($result,$i,'last_name');
			$customer_email_id = mysql_result($result,$i,'email');
			$city = mysql_result($result,$i,'city');
			$state= mysql_result($result,$i,'state');
			$address1 = mysql_result($result,$i,'address1');
			$address2= mysql_result($result,$i,'address2');
			$pincode = mysql_result($result,$i,'pincode');
			$mobile = mysql_result($result,$i,'mobile');
			$pickup_date = mysql_result($result,$i,'pick_up_date');
			$start_time = mysql_result($result,$i,'start_time');
			$end_time = mysql_result($result,$i,'end_time');
			$pickup_id =mysql_result($result,$i,'id');
			$status =mysql_result($result,$i,'status');
			$customer_id =mysql_result($result,$i,'customer_id');
			$pickup_cost =mysql_result($result,$i,'pickup_cost');
			$logistics_partner =mysql_result($result,$i,'logistics_partner');
			$waybill_number =mysql_result($result,$i,'waybill_number');
			$item_count =mysql_result($result,$i,'item_count');
			$items =mysql_result($result,$i,'items');
			$amount =mysql_result($result,$i,'amount');
			$unaccepted_action =mysql_result($result,$i,'unaccepted_action');
			$unaccepted_item_status = mysql_result($result,$i,'unaccepted_item_status');
			$querynew = "SELECT * FROM inventory WHERE pickup_id = '$pickup_id' ";
			$resultnew = mysql_query($querynew);
			
			$numresultnew = mysql_numrows($resultnew);
			$j = 0;
			$countrejection = 0;
			$countacceptance = 0;
			while ( $j < $numresultnew ){


				$itemrejectionstatus = mysql_result($resultnew,$j,'qc_status');
				if ($itemrejectionstatus == "rejected"){
					$countrejection++;
				}
				elseif ($itemrejectionstatus == "accepted") {
					$countacceptance++;
				}
				$j++;
			}

			$totalitems = $numresultnew;
		?>
		
		

		<tr>
			<td> <?php echo $i+1 ?></td>
			<form action = "pickup_seller_summary.php" method = "POST" target="_blank">
			<td><?php echo $first_name;?> </td>
			<td><?php echo $last_name;?>	</td>
			
			<?php 
				if ($customer_email_id == NULL )   // If customer has searched using date filters, then we need to extract email ID
				{
							
							$query = "SELECT email from customer_entity WHERE entity_id = '$customer_id'";
			
							$res = mysql_query($query);

							$customer_email_id = mysql_result($res,0,'email');
						
				}
					
			?>
			<td ><?php echo $customer_email_id ?></td>		
			<td><?php echo $city ;?></td>
			<td ><?php echo $state;?></td>
			<td><?php echo $address1." ".$address2; ?></td>
			<td><?php echo $pincode; ?></td>
			<td><?php echo $mobile; ?></td>
		
			<td><?php echo $pickup_date; ?></td>
			<!--td ><input type = "text" value = '<?php echo $start_time; ?>' name = "start_time" readonly = true> </td>	
			<td ><input type = "text" value = '<?php echo $end_time; ?>' name = "end_time" readonly = true> </td-->	
			<td><?php echo $pickup_id; ?></td>
			
			<td>
			<?php echo $status ?>
				
			</td>
			<!--td><input type = "number" value = '<?php echo $pickup_cost ?>' name = "pickup_cost"></td-->
			<td>
				<?php echo $logistics_partner ?>
			</td>
			<td ><?php echo $waybill_number; ?></td>	
			<td><?php echo $items ?></td>
			<td><?php echo $item_count ?></td>
			
			<td><?php echo $amount ?></td>
			<td><?php echo $unaccepted_item_status ?></td>
			
			<td><textarea name="unaccepted_action" style ="width:100px;" ><?php echo $unaccepted_action ?></textarea></td>
			<td><?php echo $totalitems; ?></td>
			<td><?php echo $countacceptance; ?></td>
			<td><?php echo $countrejection; ?></td>
			<td hidden = "true"><input type = "text" value = '<?php echo $start_date ?>' name = "start_date" readonly = true> </td>
			<td hidden = "true"><input type = "text" value = '<?php echo $end_date ?>' name = "end_date" readonly = true> </td>
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
	saveAs(txt,"Pickup_Report.xls");
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