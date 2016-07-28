<?php

require_once '../../../app/Mage.php';
Mage::app();
include_once("../../../login-with-google-using-php/config.php");
include_once("../../../login-with-google-using-php/includes/functions.php");
include '../../utils/access_block.php';
include '../db_config.php';

$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "control panel";
	$panel_access = ac_level($blockname);
	
	if($access_level <= $panel_access)
	{
		?>
		<html>
		<body>
		<a href = "../../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
		</body>
		</html>
		<?php

		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];


		/* Format our dates */
		$start_date = date('Y-m-d H:i:s', strtotime($start_date));
		$end_date = date('Y-m-d H:i:s', strtotime($end_date));

		$analysis_end_date = date('Y-m-d', strtotime("+6 months", strtotime($end_date)));

		/* Get the collection */
		/*$orders = Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$end_date))
			->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));   // Decide the state
		*/

		//Get all orders between the requested date range

		$orders = Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$end_date))
			->addAttributeToFilter('status', array('neq' => 'customer_cancellation','payment_failure','canceled','closed_undelivered','undelivered'));   // Decide the state

		if ($orders == NULL)
		{
			echo "No results found";
		}

		else 
		{
			$i = 0;
			foreach($orders as $order)
			{
				// Get the email IDs of all orders
				$email_id = $order->getShippingAddress()->getEmail();

				$order_data[$i] = $email_id;
				$i++;

			}

			$unique_emails = array_unique($order_data);

			$start_of_rekinza_date = '2015-03-08 00:00:00';
		   
			$one_day_before_analysis_start_date = date('Y-m-d', strtotime($start_date));
		   
			$one_day_before_analysis_start_date = strtotime ( '-1 day' , strtotime ( $one_day_before_analysis_start_date ) ) ;
			$one_day_before_analysis_start_date = date ( 'Y-m-d H:i:s' , $one_day_before_analysis_start_date );

			$index = 0;

			foreach($unique_emails as $email)
			{

				$orders_by_email = Mage::getModel('sales/order')->getCollection()
				->addAttributeToFilter('customer_email', $email)
				->addAttributeToFilter('created_at', array('from'=>$start_of_rekinza_date, 'to'=>$one_day_before_analysis_start_date))
				->addAttributeToFilter('status', array('neq' => 'customer_cancellation','payment_failure','canceled','closed_undelivered','undelivered','closed','closed_return','holded','return','unable_to_fulfil'));   // Decide the state

			
			//->addAttributeToFilter('status', array('neq' => 'customer_cancellation','payment_failure','canceled','closed_undelivered','undelivered'));   // Decide the state


				if($orders_by_email->getSize() == 0)
				{
					$new_customers_emails[$index] = $email; 
				}
				$index++;
			}

			//var_dump($new_customers_emails);


			//Get all the unique email IDs
			
			$within_one_month = 0;
			$within_two_months = 0;
			$within_three_months = 0;
			$repeat_customer_count = 0;
			$one_repeat = 0;
			$two_repeats = 0;
			$three_repeats = 0;
			$more_than_three_repeats = 0;
			$repeat_order_count =0;
			$total_new_order_count = 0;
			$total_new_order_count = count($new_customers_emails);

		
			// Process for each email ID
			foreach($new_customers_emails as $email)
			{

				$orders_by_email = Mage::getModel('sales/order')->getCollection()
				->addAttributeToFilter('customer_email', $email)
				->addAttributeToFilter('created_at', array('from'=>$start_date, 'to'=>$analysis_end_date))
				->addAttributeToFilter('status', array('neq' => 'customer_cancellation','payment_failure','canceled','closed_undelivered','undelivered','closed','closed_return','holded','return','unable_to_fulfil'));   // Decide the state

				
				//->addAttributeToFilter('status', array('neq' => 'customer_cancellation','payment_failure','canceled','closed_undelivered','undelivered'));   // Decide the state


				// Variable to check whether first entry of a particular email ID is to be processed
				$first_entry_check = TRUE;
				$is_repeat_customer = FALSE;   // Set to true if multiple orders of a customer are found
				$repeat_count_for_single_customer = 0;
				//$total_new_order_count = $total_new_order_count + $orders_by_email->getSize();

				foreach($orders_by_email as $order)
				{
					
					if($first_entry_check == TRUE)
					{
						$first_order_date = $order->getCreatedAt();
						$first_order_date = date('Y-m-d', strtotime($start_date));
						$first_entry_check = FALSE;
					}

					else
					{
						$repeat_count_for_single_customer = $repeat_count_for_single_customer + 1;

						$order_date = $order->getCreatedAt();
						$order_date = date('Y-m-d', strtotime($order_date));
						$repeat_order_date_diff =  ceil(strtotime($order_date) - strtotime($first_order_date))/86400;

						$repeat_order_count++;

						if($repeat_order_date_diff <31 && $is_repeat_customer == FALSE)
						{
							
							$within_one_month = $within_one_month + 1;
							$is_repeat_customer = TRUE;
						}
						else if ($repeat_order_date_diff < 61 && $is_repeat_customer == FALSE)
						{
							$within_two_months = $within_two_months + 1;
							$is_repeat_customer = TRUE;
						}
						else if ($is_repeat_customer == FALSE)
						{
							$within_three_months = $within_three_months + 1;
							$is_repeat_customer = TRUE;
						}

					}

				}

				if($is_repeat_customer == TRUE)
					{
						$repeat_customer_count = $repeat_customer_count + 1;

						if ($repeat_count_for_single_customer ==1)
						{
							$one_repeat = $one_repeat + 1;
						}
						else if ($repeat_count_for_single_customer == 2)
						{
							$two_repeats = $two_repeats + 1;
						}
						else if ($repeat_count_for_single_customer == 3)
						{
							$three_repeats = $three_repeats + 1;
						}
						else
						{
							$more_than_three_repeats = $more_than_three_repeats + 1;
						}
					}

			}
		   
			$repeat_order_percent = $repeat_order_count/$total_new_order_count * 100;
			$repeat_customer_percent = $repeat_customer_count/$total_new_order_count * 100;
			$repeat_customer_percent =round($repeat_customer_percent,2);
		
		}


	?>
	   
	   <html>
		<h1>Cohort Analysis</h1>
		   <table>
				<tr>
					<td>Start Date</td>
					<td><?php echo $start_date;?> </td>
				</tr>
				
				<tr>
					<td>End Date</td>
					<td><?php echo $end_date;?> </td>
				</tr>
				
				<tr>
					<td>New Customers Count</td>
					<td><?php echo $total_new_order_count;?> </td>
				</tr>
				
				<tr>
					<td>Repeat Customers Count</td>
					<td><?php echo $repeat_customer_count;?> </td>
				</tr>
				
				<tr>
					<td>Total Repeat Orders Count</td>
					<td><?php echo $repeat_order_count;?> </td>
				</tr>

				<tr>
					<td>First Repeat Within 1 Month</td>
					<td><?php echo $within_one_month;?> </td>
				</tr>
				
				<tr>
					<td>First Repeat Within 2 Months</td>
					<td><?php echo $within_two_months;?> </td>
				</tr>
				
				<tr>
					<td>First Repeat Within 3 Months</td>
					<td><?php echo $within_three_months;?> </td>
				</tr>
				
				<tr>
					<td>One Repeat</td>
					<td><?php echo $one_repeat;?> </td>
				</tr>
				
				<tr>
					<td>Two Repeats</td>
					<td><?php echo $two_repeats;?> </td>
				</tr>

				<tr>
					<td>Three Repeats</td>
					<td><?php echo $three_repeats;?> </td>
				</tr>

				<tr>
					<td>More Than Three Repeats</td>
					<td><?php echo $more_than_three_repeats;?> </td>
				</tr>	

				<tr>
					<td>Repeat Customers Percentage</td>
					<td><?php echo $repeat_customer_percent."%";?> </td>
				</tr>	
			
		   </table>
	   
	   </html>
   
   <?php
   	} //panelif ends
	else 
	{
		echo "not allowed for this email id";
	}
}
else
{
	echo "Sorry! You are not authorised.";
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

</style>
