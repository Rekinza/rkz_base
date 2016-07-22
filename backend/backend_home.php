<?php
include_once("../login-with-google-using-php/config.php");
include_once("../login-with-google-using-php/includes/functions.php");
include 'utils/access_block.php';
include 'db_config.php';
if($_SESSION['google_data'] == NULL)
{
	if(isset($_REQUEST['code']))
	{
		$gClient->authenticate();
		$_SESSION['token'] = $gClient->getAccessToken();
		//header('Location: ' . filter_var($redirect_url, FILTER_SANITIZE_URL));
	}
	if (isset($_SESSION['token'])) {
		$gClient->setAccessToken($_SESSION['token']);
	}
	if ($gClient->getAccessToken()) {
		$userProfile = $google_oauthV2->userinfo->get();
		//DB Insert
		//$gUser = new Users();
		//$gUser->checkUser('google',$userProfile['id'],$userProfile['given_name'],$userProfile['family_name'],$userProfile['email'],$userProfile['gender'],$userProfile['locale'],$userProfile['link'],$userProfile['picture']);
		$_SESSION['google_data'] = $userProfile; // Storing Google User Data in Session
		//header("location: account.php");
		$_SESSION['token'] = $gClient->getAccessToken();
	}
}
?>
<link href='https://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,700,500italic,700italic,900,900italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Lato:400,100,100italic,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<?php
$email_logged_in = $_SESSION['google_data']['email'];
$query = "SELECT * from user_access where email LIKE '$email_logged_in'";
$result = mysql_query($query);
$numresult =  mysql_numrows($result);
if ($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
?>
<html>

	<head></head>
	<h1>Welcome to Rekinza !</h1>

	<a href = "../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
	<div id ="inventory_panels">
		<?php 
			$blockname = "inventory panel";
			$panel_access = ac_level($blockname);;
			if($access_level <= $panel_access){ ?>
		<fieldset>
			<legend>Inventory Panels</legend>
			<a href = "inventory\inventory_new.php"><button class ="panel_button">New Inventory</button></a>
			<br><br>
			<a href = "inventory\inventory_search.php"><button class ="panel_button">Search Inventory</button></a>
			<br><br>
			<a href = "inventory\inventory_pending_upload.php"><button class ="panel_button">Inventory Pending Upload</button></a>
			
			<br><br>
			<a href = "inventory\inventory_summary.php"><button class ="panel_button">Inventory Summary</button></a>
			<br><br>
			
			<a href = "inventory\inventory_print_labels.php"><button class ="panel_button">Print Labels</button></a>
			<br><br>

			</fieldset>
		<?php } //end if 
			$blockname = "search panel";
			$panel_access = ac_level($blockname);
			if($access_level <= $panel_access){
				
		?>
			<fieldset>
				<legend>Search Panels</legend>
				<form action = "inventory\inventory_search_by_filter.php" method = "GET">
				<h3>Search SKU</h3>			
					<input type = "text" name = "sku_name">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "inventory\inventory_search_by_seller.php" method = "GET">
				<h3>Search by Seller</h3>			
					<input type = "text" name = "customer_email_id" placeholder ="Enter Email ID">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "inventory\inventory_search_by_creation_date.php" method = "POST">
				<h3>Search by Creation Date</h3>			
					Start Date:<input type = "date" name = "creation_start_date">
					End Date:<input type = "date" name = "creation_end_date">
					<br>
					Inventory Status
					<select name = "status" id = "status">
						<option name = "status" value = "all">all</option>
						<option name = "status" value = "accepted">accepted</option>
						<option name = "status" value = "rejected">rejected</option>
						<option name = "status" value = "maybe">maybe</option>					
					</select>
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
			</fieldset>
	<?php  } //end if
		$blockname = "control panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access){
			
	?>
			<fieldset>
				<legend>Control Panels</legend>
				<br>
				<a href = "config\brands_config.php"><button class ="panel_button">Brand Config</button></a>
				<br>
				<a href = "config\category\category_sorting_list.php"><button class ="panel_button">Category Config</button></a>
				<br>
				<a href = "config\sku_config.php"><button class ="panel_button">SKU Config</button></a>
				<br>
				<a href = "config\sql_strict_removal.php"><button class ="panel_button">Fix the Panel</button></a>
				<br>
				<a href = "config\cohort_analysis\cohort_analysis_input.html"><button class ="panel_button">Cohort Analysis</button></a>
				<br>
			</fieldset>
	<?php  } //end if
		$blockname = "pickup panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access){
			
	?>			
			<fieldset>
				<legend>Pick Up Panel</legend>
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Seller</h3>			
					<input type = "text" name = "customer_email_id" placeholder ="Enter Email ID">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Date Range</h3>			
					Start Date:<input type = "date" name = "start_date">
					End Date:<input type = "date" name = "end_date">
					Search By:
					<select name = "search_by_field" >
						<option value = "pick_up_date" selected = "selected">Pick up</option>
						<option value = "received_date">Received</option>
						<option value = "processing_date">Processing</option>
						<option value = "live_date">Live</option>
					</select>
					Status:<select name ="status_search_in_date_range" id = "status_search_in_date_range">
					<?php
					//$row = array('requested','scheduled','received','acknowledged','processed','follow-up');
					$row = array('all','requested','scheduled','picked-up','received','processed','priced','live','cancelled','follow-up');
					
					$option = "";
					for($j = 0; $j < 10; $j++)
					{
				
						$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
							
					}
					?>
					<?php echo $option ?>
					</select>
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Waybill Number</h3>			
					<input type = "text" name = "waybill_number" placeholder ="Enter Waybill Number">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Pick up Status</h3>
					<select name ="status_search" id = "status_search">
					<?php
					//$row = array('requested','scheduled','received','acknowledged','processed','follow-up');
					$row = array('requested','scheduled','picked-up','received','processed','priced','live','cancelled','follow-up');
					
					$option = "";
					for($j = 0; $j < 9; $j++)
					{
				
						$option .= '<option value = "'.$row[$j].'">'.$row[$j].'</option>';
							
					}
					?>
					<?php echo $option ?>
					</select>
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>
				</form>
				
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Unaccepted Status</h3>
					<select name ="unaccepted_item_status">
						<option value = 'warehouse'>warehouse</option>
						<option value = 'returned'>returned</option>
						<option value = 'donated'>donated</option>
						<option value = 'partial'>partial</option>
						<option value = 'return initiated'>return initiated</option>
						<option value = 'return dispatched'>return dispatched</option>
					</select>
						
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>
				</form>
				
				<form action = "pickup\pickup_seller_summary.php" method = "POST">
				<h3>Search by Return Dispatch Date Range</h3>			
					Start Date:<input type = "date" name = "return_dispatch_start_date">
					End Date:<input type = "date" name = "return_dispatch_end_date">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				</form>
				
			</fieldset>
	<?php  } //end if
		$blockname = "order panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access){
	?>
			<fieldset>
				<legend>Order Panels</legend>
				<form action = "orders\orders_search_by_filter.php" method = "GET">
				<h3>Search by Date Range</h3>			
					Start Date:<input type = "date" name = "orders_start_date" required = "true">
					End Date:<input type = "date" name = "orders_end_date" required = "true">
					<br><br>
					<?php
					
					require_once '../app/Mage.php';
					Mage::app();

					$orderStatusCollection = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
					$status = array();
					$status = array(
						'-1'=>'Please Select..'
					);
					
					$option = '';

					foreach($orderStatusCollection as $orderStatus) {
						$status[] = array (
							'value' => $orderStatus['status'], 'label' => $orderStatus['label']
						);
						
						
						
						if ($orderStatus['label'] == 'Pending_COD')
						{
							$option .= '<option value = "'.$orderStatus['status'].'" selected = "selected">'.$orderStatus['label'].'</option>';
						}

						else if ($orderStatus['label'] == 'Pending_Pre-Paid')  //for pending prepaid
						{
							$option .= '<option value = "'.$orderStatus['status'].'" selected = "selected">'.$orderStatus['label'].'</option>';
						}
						
						else if ($orderStatus['label'] == 'Invoiced')  //for prepaid payment completed
						{
							$option .= '<option value = "'.$orderStatus['status'].'" selected = "selected">'.$orderStatus['label'].'</option>';
						}

						else
						{
							$option .= '<option value = "'.$orderStatus['status'].'">'.$orderStatus['label'].'</option>';
						}
					}
										
					?>
					
					Status:
					<select name="status[]" id ="status" multiple required>
						<?php echo $option ?>
					</select>
					
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>				
				</form>

			</fieldset>
		<?php  } //end if
		$blockname = "cashout panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access){
		?>
			<fieldset>
				<legend>Cash Out Panel</legend>
				<form action = "cashout\cashout_summary.php" method = "POST">
				<h3>Search by Seller</h3>			
					<input type = "text" name = "customer_email_id" placeholder ="Enter Email ID">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "cashout\cashout_summary.php" method = "POST">
				<h3>Search by Date Range</h3>			
					Start Date:<input type = "date" name = "start_date">
					End Date:<input type = "date" name = "end_date">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>
					
					<form action = "cashout\cashout_summary.php" method = "POST">
				<h3>Search by Status</h3>			
					<select name ="cashout_status" id = "cashout_status">
					<?php

						include 'db_config.php';
						
						$get = mysql_query("SELECT state FROM cashout_state");
						$option = '<option value="" disabled="disabled" selected="selected">Select Type</option>';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['state'].'">'.$row['state'].'</option>';
						}					
					?>
					<?php echo $option ?>
					</select>
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			

				</form>
			</fieldset>
	<?php  } //end if
		$blockname = "returns panel";
		$panel_access = ac_level($blockname);
		if($access_level <= $panel_access){
	?>
			<fieldset>
				<legend>Returns Panel</legend>
				<form action = "returns\returns_list.php" method = "POST">
				<h3>Search by Customer</h3>			
					<input type = "text" name = "customer_email_id" placeholder ="Enter Email ID">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "returns\returns_list.php" method = "POST">
				<h3>Search by Date Range</h3>			
					Start Date:<input type = "date" name = "start_date">
					End Date:<input type = "date" name = "end_date">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "returns\returns_list.php" method = "POST">
				<h3>Search by Status</h3>			
					<select name ="return_status" id = "return_status">
					<?php
					include 'db_config.php'; 	//made a new db_config in the root itself
					$get = mysql_query("SELECT status FROM returns_status");
						$option = '<option value="" disabled="disabled" selected="selected">Select Type</option>';
						while($row = mysql_fetch_assoc($get))
						{
						  $option .= '<option value = "'.$row['status'].'">'.$row['status'].'</option>';
						}					
					?>
					<?php echo $option ?>
					</select>
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
				<form action = "returns\returns_list.php" method = "POST">
					<h3>Search by Waybill Number</h3>			
					<input type = "text" name = "waybill_number" placeholder ="Enter Waybill Number">
					<p class = "submit">
						<input type = "submit" value = "Search">
					</p>			
				</form>
				
			</fieldset>
			<?php }//end if	
			
			$blockname = "offline panel";
			$panel_access = ac_level($blockname);
			if($access_level <= $panel_access){
			?>
			<fieldset>
				<legend>Offline Orders Panel</legend>
				<br>
				<a href = "offline\input_order.php"><button class ="panel_button">Create Order</button></a>
				<br>
			</fieldset>
			
			<?php }//endif
			?>
			
		</div>

</html>

<?php
}
else
{
	echo "Sorry! You are not authorised";
}
?>	


<style>
body
{
	background-color: #eaeaea;
	font-family: 'Avenir', sans-serif;
}

h1 
{
	text-align: center;
	color: #ffffff;
	background-color: #50c7c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
	font-family: 'Avenir', sans-serif;
}

span
{
	float:left;
	display:inline-block;
	width:49%;
	font-family: 'Avenir', sans-serif;
}

h2
{
	text-align: center;
	color: #bb1515;
	background-color: #b7c3c2;
	width:50%;
	margin-left:auto;
	margin-right:auto;
	font-family: 'Avenir', sans-serif;
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
	background-color :#323232;
	color : white;
	width:100%;

}

.panel_button
{
	color: white;
	background: #50c7c2;
	border: 2px outset #50c7c2;
	font-size:1.2em;
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