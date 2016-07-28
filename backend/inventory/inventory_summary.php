root<?php
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
	$accepted_index = 1;
	$rejected_index = 2;
	$maybe_index = 3;
	$quantity = 1;

	$query = "SELECT type,sub_type,qc_status FROM inventory WHERE 1";

	$result = mysql_query($query);

	$numresult = mysql_numrows($result);

	$i = 0;

	while ( $i < $numresult )
	{
		$type = mysql_result($result,$i,'type');
		$sub_type = mysql_result($result,$i,'sub_type');
		$qc_status = mysql_result($result,$i,'qc_status');
		$quantity = mysql_result($result, $i, 'quantity');

		if ( strtolower($type) =='clothing')
		{
			$clothing[$sub_type][0] = $sub_type;
			if(strtolower($qc_status) =='accepted')
			{
				$clothing[$sub_type][$accepted_index]= $clothing[$sub_type][$accepted_index]+$quantity;
				$clothing_accepted = $clothing_accepted + $quantity;
			}
			else if(strtolower($qc_status) =='rejected')
			{
				$clothing[$sub_type][$rejected_index]= $clothing[$sub_type][$rejected_index]+$quantity;
				$clothing_rejected = $clothing_rejected + $quantity;
			}
			else
			{
				$clothing[$sub_type][$maybe_index]= $clothing[$sub_type][$maybe_index]+$quantity;
				$clothing_maybe = $clothing_maybe + $quantity;
			}
		}
		else if (strtolower($type) =='bags')
		{
		
			$bags[$sub_type][0] = $sub_type;
			if(strtolower($qc_status) =='accepted')
			{
				$bags[$sub_type][$accepted_index]= $bags[$sub_type][$accepted_index]+$quantity;
				$bags_accepted = $bags_accepted + $quantity;
			}
			else if(strtolower($qc_status) =='rejected')
			{
				$bags[$sub_type][$rejected_index]= $bags[$sub_type][$rejected_index]+$quantity;
				$bags_rejected = $bags_rejected + $quantity;
			}
			else
			{
				$bags[$sub_type][$maybe_index]= $bags[$sub_type][$maybe_index]+$quantity;
				$bags_maybe = $bags_maybe + $quantity;
			}
		}
		if (strtolower($type) =='shoes')
		{

			$shoes[$sub_type][0] = $sub_type;
			if(strtolower($qc_status) =='accepted')
			{
				$shoes[$sub_type][$accepted_index]= $shoes[$sub_type][$accepted_index]+$quantity;
				$shoes_accepted = $shoes_accepted + $quantity;			
			}
			else if(strtolower($qc_status) =='rejected')
			{
				$shoes[$sub_type][$rejected_index]= $shoes[$sub_type][$rejected_index]+$quantity;
				$shoes_rejected = $shoes_rejected+ $quantity;
			}
			else
			{
				$shoes[$sub_type][$maybe_index]= $shoes[$sub_type][$maybe_index]+$quantity;
				$shoes_maybe = $shoes_maybe+ $quantity;
			}
		}
		
		$i++;
	}

	$clothing_total = $clothing_accepted + $clothing_rejected + $clothing_maybe;
	$bags_total = $bags_accepted + $bags_rejected + $bags_maybe;
	$shoes_total = $shoes_accepted + $shoes_rejected + $shoes_maybe;

	mysql_close();

	?>


	<html>
		<head></head>
		<h1>Inventory Summary</h1>

		<div id ="inventory_summary">
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
				
			<fieldset>
				<legend>Inventory Distribution</legend>
				<table id="report_table">
				<th></th>
				<th>Accepted</th>
				<th>Rejected</th>
				<th>Maybe</th>
				<th>Total</th>			
				
				<tr></tr>

				<tr bgcolor ="#cddcd5">
					<td><b>Clothing</b></td>
					<td><b><?php echo $clothing_accepted?></b></td>
					<td><b><?php echo $clothing_rejected?></b></td>
					<td><b><?php echo $clothing_maybe?></b></td>
					<td><b><?php echo $clothing_total?></b></td>
				</tr>
				
				
				<?php
				
				foreach($clothing as $clothing_record)
				{
					$total_sub_cat_count = 0;
				?>
					<tr>
						<?php
						for ($i = 0;$i<4;$i++)
						{
						?>
							<td><?php echo $clothing_record[$i]?></td>
						<?php
							
							$total_sub_cat_count = $total_sub_cat_count + $clothing_record[$i];
						}
						?>
						<td><?php echo $total_sub_cat_count?></td>
					</tr>
					<?php
				}
				?>			
				<tr></tr>
				
				<tr bgcolor ="#cddcd5">
					<td><b>Bags</b></td>
					<td><b><?php echo $bags_accepted?></b></td>
					<td><b><?php echo $bags_rejected?></b></td>
					<td><b><?php echo $bags_maybe?></b></td>
					<td><b><?php echo $bags_total?></b></td>
				</tr>
				
				
				<?php
				foreach($bags as $bag_record)
				{
						$total_sub_cat_count = 0;
				?>
					<tr>
						<?php
						for ($i = 0;$i<4;$i++)
						{
						?>
							<td><?php echo $bag_record[$i]?></td>
						<?php
							$total_sub_cat_count = $total_sub_cat_count + $bag_record[$i];					
						}
						?>
						<td><?php echo $total_sub_cat_count?></td>
					</tr>
					<?php
				}
				?>
				<tr></tr>
				
					<tr bgcolor ="#cddcd5">
					<td><b>Shoes</b></td>
					<td><b><?php echo $shoes_accepted?></b></td>
					<td><b><?php echo $shoes_rejected?></b></td>
					<td><b><?php echo $shoes_maybe?></b></td>
					<td><b><?php echo $shoes_total?></b></td>
				</tr>
				
				
				
				<?php
				foreach($shoes as $shoes_record)
				{
					$total_sub_cat_count = 0;
				?>
					<tr>
						<?php
						for ($i = 0;$i<4;$i++)
						{
						?>
							<td><?php echo $shoes_record[$i]?></td>
						<?php
							$total_sub_cat_count = $total_sub_cat_count + $shoes_record[$i];					
						}
						?>
						<td><?php echo $total_sub_cat_count?></td>					
					</tr>
					<?php
				}
				?>		
				<tr></tr>
				
				<tr bgcolor ="#cddcd5">
					<td><b>All</b></td>
					<td><b><?php echo $clothing_accepted + $bags_accepted + $shoes_accepted?></b></td>
					<td><b><?php echo $clothing_rejected + $bags_rejected + $shoes_rejected?></b></td>
					<td><b><?php echo $clothing_maybe + $bags_maybe + $shoes_maybe?></b></td>
					<td><b><?php echo $clothing_total + $bags_total + $shoes_total?></b></td>
				</tr>
				
				</table>
			</fieldset>
		</div>

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


div
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

.panel_button
{
	color: white;
	background: #bb1515;
	border: 2px outset #d7b9c9;
	font-size:1.1em;
	border-radius:7px;
} 

a
{
	text-decoration:none;
}

a:link 
{
	color: aquamarine;
	
}

a:visited
{
	color:white;
}

a:hover
{
	color:grey;
}


</style>