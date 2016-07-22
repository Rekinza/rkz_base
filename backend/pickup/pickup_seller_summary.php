<?php
include '../db_config.php';
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
	$blockname = "pickup panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	if(isset($_POST['pickup_id']))
	{ //check if form was submitted

		$status = $_POST['status'];
		$pickup_id = $_POST['pickup_id'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$pickup_cost = $_POST['pickup_cost'];
		$logistics_partner = $_POST['logistics_partner'];
		$waybill_number = $_POST['waybill_number'];
		$item_count = $_POST['item_count'];
		$pick_up_date = $_POST['pickup_date'];
		$received_date = $_POST['received_date'];
		$processing_date = $_POST['processing_date'];
		$live_date = $_POST['live_date'];
		
		$customer_email_id = $_POST['customer_email_id'];
		$unaccepted_item_status = $_POST['unaccepted_item_status'];
		$unaccepted_action = $_POST['unaccepted_action'];
		/* Return Details */
		
		$return_payment_status =$_POST['return_payment_status'];;
		$return_tracking_id =$_POST['return_tracking_id'];;
		$return_logistics_partner =$_POST['return_logistics_partner'];;
		$return_dispatch_date = $_POST['return_dispatch_date'];

		
		include 'db_config.php';
		
		if ($waybill_number != NULL)    // If waybill number is entered, then automatically update status of order to scheduled
		{
			if ($status== 'requested') 
				$status = 'scheduled';
		}
		
		$query = "UPDATE thredshare_pickup SET first_name='$first_name',last_name='$last_name',unaccepted_action='$unaccepted_action', unaccepted_item_status='$unaccepted_item_status',status = '$status', email = '$customer_email_id', pick_up_date = '$pick_up_date', received_date = '$received_date', processing_date = '$processing_date',live_date = '$live_date',  pickup_cost = '$pickup_cost', logistics_partner = '$logistics_partner', waybill_number = '$waybill_number', item_count = '$item_count', return_payment_status ='$return_payment_status', return_tracking_id ='$return_tracking_id', return_logistics_partner = '$return_logistics_partner', return_dispatch_date = '$return_dispatch_date' WHERE id = '$pickup_id' ";
		
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
		
	$customer_email_id = $_POST['customer_email_id'];

	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$status_search_in_date_range = $_POST['status_search_in_date_range'];
	$search_by_field = $_POST['search_by_field'];
	$waybill_number = $_POST['waybill_number'];
	$status_search = $_POST['status_search'];
	$unaccepted_item_status = $_POST['unaccepted_item_status'];
	
	$return_dispatch_start_date = $_POST['return_dispatch_start_date'];
	$return_dispatch_end_date = $_POST['return_dispatch_end_date'];

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

	else if ($start_date != NULL && $end_date != NULL && $search_by_field!=NULL && $status_search_in_date_range != NULL)
	{	
		if($status_search_in_date_range != 'all')
		{
			$query = "SELECT * FROM thredshare_pickup WHERE $search_by_field BETWEEN '$start_date' AND '$end_date' AND status = '$status_search_in_date_range'";
		}
		else
		{
			$query = "SELECT * FROM thredshare_pickup WHERE $search_by_field BETWEEN '$start_date' AND '$end_date'";
		}
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
	
	else if ($unaccepted_item_status != NULL)
	{
		
		$query = "SELECT * FROM thredshare_pickup WHERE unaccepted_item_status = '$unaccepted_item_status' AND status = 'live'";  // Check only for live items	
		$result = mysql_query($query);
	}

	else if ($return_dispatch_start_date != NULL && $return_dispatch_end_date != NULL)
	{
		
		$query = "SELECT * FROM thredshare_pickup WHERE return_dispatch_date BETWEEN '$return_dispatch_start_date' AND '$return_dispatch_end_date' AND status = 'live'";
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
		
		<a target ="blank" href = '<?php echo "pickup_seller_summary_for_export.php?status_search=".$status_search."&pickup_start_date=".$pickup_start_date."&pickup_end_date=".$pickup_end_date."&return_dispatch_start_date=".$return_dispatch_start_date."&return_dispatch_end_date=".$return_dispatch_end_date?>'> Export To Excel </a>
		
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
			<th>Received Date</th>
			<th>Processing Date</th>
			<th>Live Date</th>
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
			
			<th>Return Payment Status</th>
			<th>Return Logisitics Partner</th>
			<th>Return Dispatch Date</th>
			<th>Return Tracking ID</th>
			
			<th>Unaccepted Item Status</th>
			<th>Unaccepted Action</th>
			<th>No. Of Item</th>
			<th>Accepted Count</th>
			<th>Rejected Count</th>
			<th>Inv</th>
			<th>Send Email</th>
			<th>Create Accounts</th>
			<th>Return Invoice</th>
			<th>Update</th>
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
			$received_date = mysql_result($result,$i,'received_date');
			$processing_date = mysql_result($result,$i,'processing_date');
			$live_date = mysql_result($result,$i,'live_date');
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
			
			$return_payment_status =mysql_result($result,$i,'return_payment_status');
			$return_tracking_id =mysql_result($result,$i,'return_tracking_id');
			$return_logistics_partner =mysql_result($result,$i,'return_logistics_partner');
			$return_dispatch_date =mysql_result($result,$i,'return_dispatch_date');
			
			$unaccepted_action =mysql_result($result,$i,'unaccepted_action');
			$unaccepted_item_status = mysql_result($result,$i,'unaccepted_item_status');

			//Pickup ID Items, Accepted & Rejected
			$querynew = "SELECT * FROM inventory WHERE pickup_id = '$pickup_id' ";
			$resultnew = mysql_query($querynew);
			
			$numresultnew = mysql_numrows($resultnew);
			$j = 0;
			$countrejection = 0;
			$countacceptance = 0;
			$totalitems = 0;
			while ( $j < $numresultnew ){


				$itemrejectionstatus = mysql_result($resultnew,$j,'qc_status');
				$quantity = mysql_result($resultnew, $j,'quantity');
				if ($itemrejectionstatus == "rejected"){
					$countrejection = $countrejection + $quantity;
				}
				elseif ($itemrejectionstatus == "accepted") {
					$countacceptance = $countacceptance + $quantity;
				}
				$j++;
				$totalitems = $totalitems + $quantity;
			}
			
			if($totalitems > 0)
			{
				$acceptance_ratio = $countacceptance/$totalitems;

				if ($acceptance_ratio >= 0.75  && $totalitems > 3)
				{
					$powerpacket = 1;
				}
				else
				{
					$powerpacket = 0;
				}
			}
			else
			{
					$powerpacket = 0;
			}

		
		?>
		
		

		<tr>
			<td> <?php echo $i+1 ?></td>
			<form action = "pickup_seller_summary.php" method = "POST" target="_blank">
			<td><input type = "text" value = '<?php echo $first_name;?>' name = "first_name" >	</td>
			<td><input type = "text" value = '<?php echo $last_name;?>' name = "last_name" >	</td>
			
			<?php 
				if ($customer_email_id == NULL )   // If customer has searched using date filters, then we need to extract email ID
				{
							
							$query = "SELECT email from customer_entity WHERE entity_id = '$customer_id'";
			
							$res = mysql_query($query);

							$customer_email_id = mysql_result($res,0,'email');
						
				}
					
			?>
			<td ><input type = "text" value = '<?php echo $customer_email_id ?>' name = "customer_email_id" > </td>		
			<td><?php echo $city ;?></td>
			<td ><?php echo $state;?></td>
			<td><?php echo $address1." ".$address2; ?></td>
			<td><?php echo $pincode; ?></td>
			<td><?php echo $mobile; ?></td>
		
			<td><input type = "date" name = "pickup_date" value = <?php echo $pickup_date; ?> ></td>
			<td><input type = "date" name = "received_date" value = <?php echo $received_date; ?> ></td>
			<td><input type = "date" name = "processing_date" value = <?php echo $processing_date; ?> ></td>
			<td><input type = "date" name = "live_date" value = <?php echo $live_date; ?> ></td>
			<td><input type = 'text' name = "pickup_id" value = <?php echo $pickup_id; ?> readonly = true> </td>
			
			<td>
			<?php
					//$row = array('requested','scheduled','received','acknowledged','processed','follow-up');
					$row = array('requested','scheduled','picked-up','received','processed','priced','live','cancelled','follow-up');
					
					$option = "";
					for($j = 0; $j < 9; $j++)
					{
						if($row[$j] != $status)	
							$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
						else
								$option .= '<option value = "'.$row[$j].'" selected = "selected">'.$row[$j].'</option>';
							
					}
				?>
				<select name ="status">
					<?php echo $option ?>
				</select>
			</td>
			<!--td><input type = "number" value = '<?php echo $pickup_cost ?>' name = "pickup_cost"></td-->
			<td>
			<?php
					$get = mysql_query("SELECT partner_name FROM logistics_partner where 1");
					//$option = '<option value="" disabled="disabled" selected="selected">Select Partner</option>';
					$option = '';
					$partner_found_flag = 0 ; //flag to check if assigned logistics partner for a pick up is found
					while($row = mysql_fetch_assoc($get))
					{
						if($row['partner_name'] == $logistics_partner)	
						{		
								$option .= '<option value = "'.$row['partner_name'].'" selected = "selected">'.$row['partner_name'].'</option>';
								$partner_found_flag = 1;
						}
						else if (($row['partner_name'] == 'NuvoEx') && ($partner_found_flag == 0))
						{
								$option .= '<option value = "'.$row['partner_name'].'" selected = "selected">'.$row['partner_name'].'</option>';
						}
						else
						{	
								$option .= '<option value = "'.$row['partner_name'].'">'.$row['partner_name'].'</option>';
						}	
					}
			?>
			
				<select name ="logistics_partner">
					<?php echo $option ?>
				</select>
			</td>
			<td ><input type = "text" value = '<?php echo $waybill_number; ?>' name = "waybill_number"> </td>	
			<td><?php echo $items ?></td>
			<td><input type = "number" value = '<?php echo $item_count ?>' name = "item_count" onkeypress ='validate(event)'></td>
			
			<td><input type = "number" value = '<?php echo $amount ?>' name = "amount" onkeypress ='validate(event)' readonly = true></td>
			
			<td>
			<?php

					//status for rejected items. Not retrieved from any table.
					$row = array('COD','Prepaid requested','Prepaid paid');
					
					$option = '<option value="" disabled="disabled" selected="selected">Payment Status</option>';
					for($j = 0; $j < 3; $j++)
					{
						 if($row[$j] != $return_payment_status)	
						 	$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
						 else
								$option .= '<option value = "'.$row[$j].'" selected = "selected">'.$row[$j].'</option>';
							
					}
				?>
				<select name ="return_payment_status">
					<?php echo $option ?>
				</select>
				
				<td>
				<?php
						$get = mysql_query("SELECT partner_name FROM logistics_partner where 1");
						//$option = '<option value="" disabled="disabled" selected="selected">Select Partner</option>';
						$option = '';
						$partner_found_flag = 0 ; //flag to check if assigned logistics partner for a pick up is found
						while($row = mysql_fetch_assoc($get))
						{
							if($row['partner_name'] == $return_logistics_partner)	
							{		
									$option .= '<option value = "'.$row['partner_name'].'" selected = "selected">'.$row['partner_name'].'</option>';
									$partner_found_flag = 1;
							}
							else if (($row['partner_name'] == 'NuvoEx') && ($partner_found_flag == 0))
							{
									$option .= '<option value = "'.$row['partner_name'].'" selected = "selected">'.$row['partner_name'].'</option>';
							}
							else
							{	
									$option .= '<option value = "'.$row['partner_name'].'">'.$row['partner_name'].'</option>';
							}	
						}
				?>
				
					<select name ="return_logistics_partner">
						<?php echo $option ?>
					</select>
				</td>
				
				<td><input type = "date" name = "return_dispatch_date" value = <?php echo $return_dispatch_date; ?> ></td>
			</td>
			<td ><input type = "text" value = '<?php echo $return_tracking_id; ?>' name = "return_tracking_id"> </td>	
			<td>
			<?php

					//status for rejected items. Not retrieved from any table.
					$row = array('warehouse','returned','donated','partial','no unaccepted items','return initiated','return dispatched');
					
					$option = '<option value="" disabled="disabled" selected="selected">Unaccepted Status</option>';
					for($j = 0; $j < 7; $j++)
					{
						 if($row[$j] != $unaccepted_item_status)	
						 	$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
						 else
								$option .= '<option value = "'.$row[$j].'" selected = "selected">'.$row[$j].'</option>';
							
					}
				?>
				<select name ="unaccepted_item_status">
					<?php echo $option ?>
				</select>
			</td>
			
			<td><textarea name="unaccepted_action" style ="width:100px;" ><?php echo $unaccepted_action ?></textarea></td>
			<!--Pickup ID Items, Accepted & Rejected-->
			<td><?php echo $totalitems; ?></td>
			<td><?php echo $countacceptance; ?></td>
			<td><?php echo $countrejection; ?></td>

			<td><a href = <?php echo "../inventory/inventory_search_by_seller.php?pickup_id=".$pickup_id?>>Inv</a></td>
			<td hidden = "true"><input type = "text" value = '<?php echo $start_date ?>' name = "start_date" readonly = true> </td>
			<td hidden = "true"><input type = "text" value = '<?php echo $end_date ?>' name = "end_date" readonly = true> </td>
			<td hidden = "true"><input type = "text" value = '<?php echo $search_by_field ?>' name = "search_by_field" readonly = true> </td>
			
			<td>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=pickedup&waybill_number=".$waybill_number."&email_id=".$customer_email_id."&name=".$first_name."&pickup_id=".$pickup_id."&mobile=".$mobile?>'>Picked up Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=received&waybill_number=".$waybill_number."&email_id=".$customer_email_id."&name=".$first_name."&pickup_id=".$pickup_id."&mobile=".$mobile?>'>Received Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=processed&waybill_number=".$waybill_number."&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name."&mobile=".$mobile."&powerpacket=".$powerpacket?>'>Processed Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=priced&waybill_number=".$waybill_number."&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name."&mobile=".$mobile?>'>Priced Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=live&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name."&mobile=".$mobile."&powerpacket=".$powerpacket?>'>Items Live Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=cancelled&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name."&mobile=".$mobile?>'>Cancelled Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=return_initiated&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name?>'>Return Initiated Email</a>
				<a href = '<?php echo "pickup_prepare_email.php?email_type=return_dispatched&return_tracking_id=".$return_tracking_id."&email_id=".$customer_email_id."&pickup_id=".$pickup_id."&name=".$first_name?>'>Return Dispatched Email</a>
			</td>
			<td>
				<a target="_blank" href = '<?php echo "pickup_create_accounts.php?email_id=".$customer_email_id."&first_name=".$first_name."&last_name=".$last_name."&mobile=".$mobile.'&pickup_id='.$pickup_id?>'>Create buyer and seller accounts</a>
			</td>
			
			<td>
				<a target="_blank" href = '<?php echo "pickup_return_invoice_generate.php?pickup_id=".$pickup_id."&item_count=".$countrejection."&first_name=".$first_name."&last_name=".$last_name.'&address1='.$address1."&address2=".$address2."&city=".$city."&state=".$state."&pincode=".$pincode."&mobile=".$mobile."&return_payment_status=".$return_payment_status?>'>Return Invoice</a>
			</td>
			
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
} //numrows if ends
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