<?php
include '../db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';
include '../../app/Mage.php';
Mage::app();

$email_logged_in ="nikita.sahai@gmail.com";//$_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "returns panel";
	$panel_access = ac_level($blockname);
	 if($access_level <= $panel_access)
	{
	if(isset($_POST['id']))
	{ //check if form was submitted

		$id = $_POST['id'];
		$pickup_date = $_POST['pickup_date'];
		$refund_mode = $_POST['refund_mode'];
		$status = $_POST['status'];
		$comments = $_POST['comments'];
		$waybill_number = $_POST['waybill_number'];
		$logistics_partner = $_POST['logistics_partner'];
			
		include 'db_config.php';
		
		if ($waybill_number != NULL)    // If waybill number is entered, then automatically update status of order to scheduled
		{
			if ($status== 'requested')
				$status = 'scheduled';
		
		}
		
		$query = "UPDATE thredshare_returns SET logistics_partner = '$logistics_partner', waybill_number = '$waybill_number', pickup_date = '$pickup_date', refund_mode = '$refund_mode', status = '$status', comments = '$comments' WHERE id = '$id' ";
		

		//updating in table
		$result = mysql_query($query);
		
		if ($result == 'TRUE')
		{
			echo 'Record Updated Successfully for Panel';
		}
		else
		{
			echo 'Record Update Failed for panel only';
		}


		mysql_close();

	}

		
	$customer_email_id = $_POST['customer_email_id'];

	$start_date = $_POST['start_date'];
	$end_date = $_POST['end_date'];
	$return_status = $_POST['return_status'];
	$waybill_number = $_POST['waybill_number'];

	if ($customer_email_id != NULL)
	{

		/************* search based on customer email id********************/
		$query = "SELECT * FROM thredshare_returns WHERE email = '$customer_email_id'";

		$result = mysql_query($query);
		
	}

	else if ($start_date != NULL && $end_date != NULL)
	{
		
		$query = "SELECT * FROM thredshare_returns WHERE pickup_date BETWEEN '$start_date' AND '$end_date' ";
		$result = mysql_query($query);
	}

	else if ($return_status != NULL)
	{
		$query = "SELECT * FROM thredshare_returns WHERE status = '$return_status'";
		$result = mysql_query($query);

	}

	else if ($waybill_number != NULL)
	{
		$query = "SELECT * FROM thredshare_returns WHERE waybill_number = '$waybill_number'";
		$result = mysql_query($query);

	}

	$numresult = mysql_numrows($result);

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
		<div id ="pickup_date_table">
			<h1>Return Details</h1>
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
				<button id="btnExport" onclick="fnExcelReport();" > Export To Excel </button>
			
			<table id="report_table">
				<th  hidden = "true">S. No</th>
				<th>Order ID</th>
				<th>Email ID</th>
				<th>Items</th>
				<th>Reason</th>
				<th>City</th>
				<th>State</th>
				<th>Address</th>
				<th>Pin-Code</th>
				<th>Mobile</th>
				<th>Pick Up Date</th>
				<!--th>Start Time</th>
				<th>End Time</th>
				<th>Refund Mode</th>
				<th>Account Details</th-->
				<th>Status</th>
				<th>Logistics Partner</th>
				<th>Waybill</th>
				<th>Comments</th>
				<th>Send Email</th>
		<?php
		$i = 0;
		while ( $i < $numresult )
		{
				$id = mysql_result($result,$i,'id');
				$customer_email_id  = mysql_result($result,$i,'email');
				$city = mysql_result($result,$i,'city');
				$state= mysql_result($result,$i,'state');
				$address1 = mysql_result($result,$i,'address1');
				$address2= mysql_result($result,$i,'address2');
				$pincode = mysql_result($result,$i,'pincode');
				$mobile = mysql_result($result,$i,'mobile');
				$pickup_date = mysql_result($result,$i,'pickup_date');
				$start_time = mysql_result($result,$i,'start_time');
				$end_time = mysql_result($result,$i,'end_time');
				$order_id =mysql_result($result,$i,'order_id');
				
				$reason =mysql_result($result,$i,'reason');
				$refund_mode =mysql_result($result,$i,'refund_mode');
				$items =mysql_result($result,$i,'items');
				$acc_holder =mysql_result($result,$i,'acc_holder');
				$acc_number =mysql_result($result,$i,'acc_number');
				$ifsc_code =mysql_result($result,$i,'ifsc_code');
				$status =mysql_result($result,$i,'status');
				$comments =mysql_result($result,$i,'comments');
				$logistics_partner=mysql_result($result,$i, 'logistics_partner');
				$waybill_number=mysql_result($result,$i, 'waybill_number');
				
			?>
			
			

			<tr>
				
				<form action = "returns_list.php" method = "POST" target="_blank" >
				<td hidden = "true"><input type = "text" value = '<?php echo $id ?>' name = "id" hidden = "true"> </td>		
				<td style ="width:200px;"><input type = "number" value = '<?php echo $order_id; ?>' name = "order_id" readonly = true>	</td>
				<td ><input type = "text" value = '<?php echo $customer_email_id  ?>' name = "customer_email_id" readonly = true> </td>		
				<td ><input type = "text" value = '<?php echo $items ?>' name = "items"> </td>		
				<td ><?php echo $reason ?></td>		
				<td><?php echo $city ;?></td>
				<td ><?php echo $state;?></td>
				<td><?php echo $address1." ".$address2; ?></td>
				<td><?php echo $pincode; ?></td>
				<td><?php echo $mobile; ?></td>
				<td><input type = "date" name = "pickup_date" value = <?php echo $pickup_date; ?>></td>
			
				<!--td style ="width:60px;"><input type = "text" value = '<?php echo $start_time; ?>' name = "start_time" readonly = true> </td>	
				<td style ="width:60px;"><input type = "text" value = '<?php echo $end_time; ?>' name = "end_time" readonly = true> </td>	

				<td>
				<?php
						
						$order = new Mage_Sales_Model_Order();
						$order->loadByIncrementId($order_id);
						//$payment_method = $order->getPayment()->getMethodInstance()->getTitle();

						$row = array('Kinzacash','Bank Transfer','Donate to Charity','Refund to Debit Card/Credit Card/Netbanking');	

						$looptill = 4;
						/*if ($payment_method != 'Debit Card/Credit Card/Netbanking')
						{
						$looptill = 3;
						}
						else
						{
						$looptill = 4;
						}
						*/
						$option = "";
						for($j = 0; $j < $looptill; $j++)
						{
							if($row[$j] != $refund_mode)	
								$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
							else
								$option .= '<option value = "'.$row[$j].'" selected = "selected">'.$row[$j].'</option>';
								
						}
				?>
				<select name ="refund_mode">
					<?php echo $option ?>
				</select>
				</td>
				
				<td><textarea name="acc_details" style ="width:100px;" ><?php echo $acc_holder." ".$acc_number." ".$ifsc_code ?></textarea></td-->
				<td>
				<?php
						$get = mysql_query("SELECT status FROM returns_status");
						
						$row = array('requested','initiated','scheduled','items received','closed','rejected');
						
						$option = "";
						while($row = mysql_fetch_assoc($get))
							{
							 if($row['status'] != $status)	
						 		$option .= '<option value = "'.$row['status'].'">'.$row['status'].'</option>';
	  					 	 else
						 		$option .= '<option value = "'.$row['status'].'" selected = "selected">'.$row['status'].'</option>';
							}
				?>
				<select name ="status">
					<?php echo $option ?>
				</select>
				</td>
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
				<td><input type = "text" value = '<?php echo $waybill_number; ?>' name = "waybill_number"></td>
				<td><textarea name="comments" style ="width:50px;" ><?php echo $comments; ?></textarea></td>
				<td><a href = '<?php echo "returns_prepare_email.php?email_type=picked-up&waybill_number=".$waybill_number."&email_id=".$customer_email_id."&returns_id=".$id?>'>Picked-up Email</a>
					<a href = '<?php echo "returns_prepare_email.php?email_type=received&email_id=".$customer_email_id."&returns_id=".$id?>'>Received Email</a>
					<button type="button" onclick="refund('<?php echo $id ?>')"> REFUND </button>
				<!--parameters to be passed for mailing -->
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
	saveAs(txt,"Returns_Report.xls");
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

function refund(id) 
{
	
	jQuery.ajax({
		type: 'POST',
		url: 'return_issue_creditmemo.php',
		data: 
		{
			'id' : id
		},
		async: false,
		success:function(message) {

			alert(message);
			
		}
	});
    
      
}
</script>