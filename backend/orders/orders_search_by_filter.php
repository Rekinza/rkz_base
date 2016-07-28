<?php

require_once '../../app/Mage.php';
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
	$blockname = "order panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	Mage::app();

	$orders_start_date = $_GET['orders_start_date']; 
	$orders_end_date = $_GET['orders_end_date']; 
	$status = $_GET['status'];

	/* Format our dates */
	$orders_start_date = date('Y-m-d H:i:s', strtotime($orders_start_date));
	$orders_end_date = date('Y-m-d H:i:s', strtotime($orders_end_date));


	/* Set end date time till 23 hrs, 59 min and 59 seconds i.e. EOD */
	$orders_end_date = strtotime ( '+23 hours' , strtotime ($orders_end_date )) ;
	$orders_end_date = date('Y-m-d H:i:s', $orders_end_date);

	$orders_end_date = strtotime ( '+59 minutes' , strtotime ($orders_end_date ));
	$orders_end_date = date('Y-m-d H:i:s', $orders_end_date);

	$orders_end_date = strtotime ( '+59 seconds' , strtotime ($orders_end_date ));
	$orders_end_date = date('Y-m-d H:i:s', $orders_end_date);

echo $orders_start_date."<br>";
echo $orders_end_date."<br>";
 
	/* Get the collection */
	$orders = Mage::getModel('sales/order')->getCollection()
	    ->addAttributeToFilter('created_at', array('from'=>$orders_start_date, 'to'=>$orders_end_date))
	    ->addAttributeToFilter('status', array('in' => $status));
		
	$products_array = array();	

	$index = 0;

	?>


	<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
	<table>
		<th>S.no</th>
		<th><input type ="checkbox" onchange ="checkAll(this)" name ="chk">Select All</th>
		<th>Order ID</th>
		<th>Customer Name</th>
		<th>Previous Orders</th>
		<th>Shipping Address</th>
		<th>Products</th>
		
		<form action = 'orders_generate_product_list.php' method = 'POST'>

		<?php
		$index = 0;
			
		foreach($orders as $order)
		{
			$index++;
			$order_items = $order->getAllVisibleItems();
			
			$order_id = $order->getId();
			
			$increment_id = $order->getIncrementId();
			
			$order_status = $order->getStatusLabel();
			
			$customer_name = $order->getCustomerName();
			
			$shipping_address = $order->getShippingAddress()->format('html');

			$customer_email = $order->getCustomerEmail();

			$prev_orders = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('customer_email', $customer_email);
				
 			if( (strpos($shipping_address,"Offline") != FALSE ) || (strpos($shipping_address,"offline") != FALSE ) )
			{
				continue;
			}
		
		?>
			<tr>
			<td><?php echo $index; ?> </td>
			<td><input type = "checkbox" name = "checkbox[]" value = "<?php echo $order_id?>"> </td>
			<td><?php echo $increment_id." ".$order_status?></td>
			<td><?php echo $customer_name ?></td>
			<td>
				<?php
				if($prev_orders->getSize() > 1)
				{		
					foreach($prev_orders as $prev_order) 
					{
						if($prev_order->getIncrementId() != $increment_id){
						
							if($prev_order->getStatus() == "closed_undelivered" || $prev_order->getStatus() == "closed"){
								?>
								<p style="color:red;"><?php echo $prev_order->getIncrementId()." ".$prev_order->getStatus(); ?> </p><br>
								<?php
							}
							elseif($prev_order->getStatus() == "closed_return" || $prev_order->getStatus() == "customer_cancellation" || $prev_order->getStatus() == "return"){
								?>
								<p style="color:orange;"><?php echo $prev_order->getIncrementId()." ".$prev_order->getStatus(); ?> </p><br>
								<?php
							}
							else{
								?>
								<p><?php echo $prev_order->getIncrementId()." ".$prev_order->getStatus(); ?></p></br>
								<?php
							}
						}
					}
				}
				else{
					?>
					<p> New Customer</p>
					<?php
				}

				?>
			</td>
			<td><?php echo $shipping_address ?></td>
				
			<?php
			foreach($order_items as $sItem) 
			{			
				?>
				<td>
					<table>
						
						<tr><img src = "<?php echo Mage::helper('catalog/image')->init($sItem->getProduct(), 'thumbnail')->resize(185, 256); ?>"/> </tr>
						<tr><?php echo $sItem->getSku(); ?> </tr>
						
					</table>
				</td>
				
				<?php
				
			}
			
		}

		?>
			</tr>
			<br>
			<p class ="submit">
				<input type = "Submit" id = "generate_product_list" name ="generate_product_list" value = "Generate Product List">
			</p>
		</form>
	</table>
	<?php
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
	
</style>

<script>
 function checkAll(ele) {
     var checkboxes = document.getElementsByName('checkbox[]');
     if (ele.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = true;
             }
         }
     } else {
         for (var i = 0; i < checkboxes.length; i++) {
             console.log(i)
             if (checkboxes[i].type == 'checkbox') {
                 checkboxes[i].checked = false;
             }
         }
     }
 }
</script>

</script>