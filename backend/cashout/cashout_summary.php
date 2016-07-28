<?php
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include 'db_config.php';
include '../utils/access_block.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "cashout panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{

		if(isset($_POST['cashout_id']))
		{ //check if form was submitted

			$state = $_POST['state'];
			$comments = $_POST['comments'];
			$cashout_id = $_POST['cashout_id'];
			$customer_id = $_POST['customer_id'];
			$beneficiary_flag = $_POST['beneficiary_flag'];
			if ($beneficiary_flag=="YES") 
			{
				$beneficiary_flag_boolean = 1;
			}
			elseif ($beneficiary_flag == "NO") 
			{
				$beneficiary_flag_boolean =0;
			}	
			
			$query = "UPDATE mw_rewardpoints_cashout SET state = '$state', comments = '$comments' WHERE id = '$cashout_id' ";
			
			$query2 = "UPDATE customer_entity SET beneficiary_flag = '$beneficiary_flag_boolean' WHERE entity_id = '$customer_id' ";
			//beneficary_flag is a boolean flag in customer_entity table with default value 0
			
			$result = mysql_query($query);
			$result2 = mysql_query($query2);
			
			if ($result == 'TRUE')
			{
				echo 'Record Updated Successfully in mw_rewardpoints_cashout'."<br>";
			}
			else
			{
				echo 'Record Update Failed in mw_rewardpoints_cashout'."<br>";
			}
			
			if ($result2 == 'TRUE')
			{
				echo 'Record Updated Successfully in customer_entity'."<br>";
			}
			else
			{
				echo 'Record Update Failed in customer_entity'."<br>";
			}
			
			mysql_close();

		}

		include 'db_config.php';
			
		$customer_email_id = $_POST['customer_email_id'];

		$start_date = $_POST['start_date'];
		$end_date = $_POST['end_date'];
		$cashout_state = $_POST['cashout_status'];


		if ($customer_email_id != NULL)
		{
			/************* customer email id and beneficiary_flag to be taken from customer entity********************/
			$query = "SELECT entity_id, beneficiary_flag from customer_entity WHERE email = '$customer_email_id'";

			
			$result = mysql_query($query);
			
			$customer_id = mysql_result($result,0,'entity_id');
			$beneficiary_flag = mysql_result($result,0,'beneficiary_flag');
			
			
			$query = "SELECT * FROM mw_rewardpoints_cashout WHERE customer_id = '$customer_id'" ;
			$result = mysql_query($query);
		}

		else if ($start_date != NULL && $end_date != NULL)
		{
			
			$query = "SELECT * FROM mw_rewardpoints_cashout WHERE timestamp BETWEEN '$start_date' AND '$end_date' ";
			$result = mysql_query($query);
		}

		else if($cashout_state != NULL)
		{

			$query = "SELECT * FROM mw_rewardpoints_cashout WHERE state = '$cashout_state'";
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
				<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			<div id ="pickup_date_table">
				<h1>Cash out Details</h1>
				
				<button id="btnExport" onclick="fnExcelReport();" > Export To Excel </button>
				<button id="btnHome"> Home </button><a href = "..\backend_home.php"></button></a>

				<table id="report_table">
					<th>S. No</th>
					<th>Email ID</th>
					<th>Message</th>
					<th>Name</th>
					<th>Bank</th>
					<th>Branch</th>
					<th>Acc No.</th>
					<th>IFSC</th>
					<th>Acc type</th>
					<th>Points</th>
					<th>Timestamp</th>
					<th>State</th>
					<th>Added As Beneficiary</th>
					<th>Comments</th>
			<?php
			$i = 0;
			while ( $i < $numresult )
			{
					$cashout_id = mysql_result($result,$i,'id');
					$customer_id = mysql_result($result,$i,'customer_id');
					$message= mysql_result($result,$i,'message');
					$name= mysql_result($result,$i,'name');
					$bank_name= mysql_result($result,$i,'bank_name');
					$branch_name= mysql_result($result,$i,'branch_name');
					$account_no= mysql_result($result,$i,'account');
					$ifsc= mysql_result($result,$i,'ifsc');
					$acc_type= mysql_result($result,$i,'account_type');
					$type = mysql_result($result,$i,'type');
					$points= mysql_result($result,$i,'points');
					$timestamp = mysql_result($result,$i,'timestamp');
					$state = mysql_result($result,$i,'state');
					$comments = mysql_result($result,$i,'comments');
					
				?>
				
				

				<tr>
					<td> <?php echo $i+1 ?></td>
					<form action = "cashout_summary.php" target="_blank" method = "POST">
					
					<?php 
						if ($start_date != NULL || $cashout_state != NULL)   // If user has searched using date filters, then we need to extract email ID
						{
							$query = "SELECT email,beneficiary_flag from customer_entity WHERE entity_id = '$customer_id'";
							
							$res = mysql_query($query);

							$customer_email = mysql_result($res,0,'email');
							$beneficiary_flag = mysql_result($res,0,'beneficiary_flag');
							
							?>
							<td><textarea name="customer_email" style ="width:150px;" readonly = "true"><?php echo $customer_email; ?></textarea></td>
							<?php					
						}
						else
						{	
						?>
							<td><textarea name="customer_email_id" style ="width:150px;" readonly = "true"><?php echo $customer_email_id; ?></textarea></td>
						<?php
						}
						?>
					<td><?php echo $message ;?></td>
					<td><?php echo $name ;?></td>
					<td><?php echo $bank_name ;?></td>
					<td><?php echo $branch_name ;?></td>
					<td><?php echo $account_no ;?></td>
					<td><?php echo $ifsc ;?></td>
					<td ><?php echo $acc_type;?></td>
					<td><?php echo $points; ?></td>
					<td><?php echo $timestamp; ?></td>

					<td>
					<?php
							$get = mysql_query("SELECT state FROM cashout_state");
							$option = '';
							
							while($row = mysql_fetch_assoc($get))
								{
								 if($row['state'] != $state)	
							 		$option .= '<option value = "'.$row['state'].'">'.$row['state'].'</option>';
							 	else
							 			$option .= '<option value = "'.$row['state'].'" selected = "selected">'.$row['state'].'</option>';
								}
						?>
						<select name ="state">
							<?php echo $option ?>
						</select>
					</td>
					<td> 
						
						<?php

							//To avoid loops here		

							$option_a = "";
							$option_b="";
							if( $beneficiary_flag != 0)
							{
								$option_a .= '<option value = "YES" selected = "selected">'."YES".'</option>';
								$option_b .= '<option value = "NO">'."NO".'</option>';
							}
							else
							{
								$option_a .= '<option value = "NO" selected = "selected">'."NO".'</option>';
								$option_b .= '<option value = "YES">'."YES".'</option>';
							}

						?>
						<select name ="beneficiary_flag">
							<?php echo $option_a;
								echo $option_b; ?>
							

						</select>	
					</td>
					<td><textarea name="comments" style ="width:80px;"><?php echo $comments; ?></textarea></td>
					<!--emailtype is always approved-->
					<td><a href = '<?php echo "cashout_prepare_email.php?email_type=approved&email_id=".$customer_email."&cashout_id=".$cashout_id?>'>Approved Email</a>
					</td>
				
					<td hidden = "true"><input type = "text" value = '<?php echo $cashout_id ?>' name = "cashout_id" readonly = true> </td>
					<td hidden = "true"><input type = "text" value = '<?php echo $start_date ?>' name = "start_date" readonly = true> </td>
					<td hidden = "true"><input type = "text" value = '<?php echo $end_date ?>' name = "end_date" readonly = true> </td>
					<!--customer id required to for customer_entity table -->
					<td hidden = "true"><input type = "text" value = '<?php echo $customer_id ?>' name = "customer_id" readonly = true> </td>
					
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
		echo "Sorry! Not authorized.";
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

#btnHome
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

.panel_button
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 

</style>

<script>

function fnExcelReport()
{
	var tab_text="<table><tr>";
    var textRange;
    tab = document.getElementById('report_table'); // id of actual table on your page

	console.log(tab.rows.length);
    for(j = 0 ; j < tab.rows.length ; j) 
    {   
        tab_text=tab_texttab.rows[j].innerHTML;
        tab_text=tab_text"</tr><tr>";
    }

    tab_text = tab_text"</tr></table>";

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