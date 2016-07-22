<?php

include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';
include 'db_config.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if($numresult > 0)
{
	
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "inventory panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{	
		require_once '../../app/Mage.php';
		Mage::app();

		$query = "SELECT DISTINCT customer_email_id FROM `inventory` ";

		$result = mysql_query($query);

		$numresult = mysql_numrows($result);

		$i = 0;
		
		$k = 0;

		?>

		<table>
		<th>S. no</th>
		<th>Email ID</th>
		<th>Order Count</th>
		<th>Unsold Items</th>
		<th>Phone Number</th>
		<?php
		while ($i < $numresult)
		{
			$seller = mysql_result($result,$i,'customer_email_id');
			
			/************* customer email id to be taken from customer entity********************/
			
			$query_mobile = "SELECT * FROM thredshare_pickup WHERE email = '$seller'" ;
			$result_mobile = mysql_query($query_mobile);

			$numresult_mobile = mysql_numrows($result_mobile);
			
			if ($numresult_mobile == 0)
				{
				/************* customer email id to be taken from customer entity********************/
				$query_mobile = "SELECT entity_id from customer_entity WHERE email = '$seller'";

				
				$result_mobile = mysql_query($query_mobile);
				
				$customer_id = mysql_result($result_mobile,0,'entity_id');
				
				$query_mobile = "SELECT * FROM thredshare_pickup WHERE customer_id = '$seller'" ;
				$result_mobile = mysql_query($query_mobile);
				$numresult_mobile = mysql_numrows($result_mobile);
			}
			
			if($numresult_mobile > 0)
			{
				$mobile = mysql_result($result_mobile,0,'mobile');	
			}
			else
			{
				$mobile = 'N/A';
			}
			
			$query_inventory = "SELECT sku_name, qc_status FROM inventory WHERE customer_email_id = '".$seller."'";
			
			$result_inventory = mysql_query($query_inventory); 
			$numresult_inventory = mysql_numrows($result_inventory);
			$j = 0;
			
			$order_count_per_seller = 0;
			$unsold_count_per_seller = 0;
			
			while ($j < $numresult_inventory)
			{
		
				$sku_name = mysql_result($result_inventory,$j,'sku_name');
				$qc_status = mysql_result($result_inventory,$j,'qc_status');		
				
				if( $qc_status == 'accepted' && ( ( 0 === strpos($sku_name, 'B-')) || (0 === strpos($sku_name, 'S-')) )  )
				{
					$q = "SELECT o.increment_id , o.status 
					FROM sales_flat_order o 
					INNER JOIN sales_flat_order_item oi ON oi.order_id = o.entity_id
					WHERE oi.sku='".$sku_name."' 
					ORDER BY o.increment_id DESC";

					$res = mysql_query($q);
					$numres = mysql_numrows($res);
					$order_id = mysql_result($res,0,'increment_id');
					$order_status = mysql_result($res,0,'status');
							
					if($numres > 0)
					{
						if($order_status =="really_confirmed")
						{
							$order_count_per_seller++;
						}
						else
						{
							$unsold_count_per_seller++;
						}
					}
					else
					{
						$unsold_count_per_seller++;
					}
					

				
				}

				
				$j++;
			}
			
			if ($order_count_per_seller > 3)
			{
				$k++;
				?>
				<tr>
					<td><?php echo $k;?></td>
					<td><?php echo $seller;?></td>
					<td><?php echo $order_count_per_seller; ?> </td>
					<td><?php echo $unsold_count_per_seller; ?> </td>
					<td><?php echo $mobile; ?> </td>
				</tr>
				<?php
			}

			
			$i++;
		}

		}

	else
	{
		echo "Sorry. Not authorised";
	}
}
else
{
	echo "Sorry. Account doesnt exist";
}

?>