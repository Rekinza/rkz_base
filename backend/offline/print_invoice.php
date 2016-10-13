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
	$blockname = "offline panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{

		//print_r($_GET);
		$firstname = $_GET['firstname'];
		$lastname = $_GET['lastname'];
		$email = $_GET['email'];
		$phone = $_GET['phone'];
		$payment_method = $_GET['payment'];
		$grandtotal = $_GET['grandtotal'];
		$allskus = $_GET['skus'];
		$allprices = $_GET['prices'];
		$alltaxes = $_GET['taxes'];
		$allproductnames = $_GET['productnames'];
		$allsubtotals = $_GET['subtotals'];

		$arrayskus = explode(",",$allskus);
		$arrayprices = explode(",", $allprices);
		$arraytaxes = explode(",", $alltaxes);
		$arrayproductnames = explode(",", $allproductnames);
		$arraysubtotals = explode(",", $allsubtotals);
		
		$skucount = count($arrayskus);
		//$pricecount = count($allprices);
		//$taxcount = count($arraytaxes);
		// $subtotal = array();
		// //$grandtotal = 0;

		// for ($i = 0; $i<$skucount; $i++ ) {
		// 		echo $arrayskus[$i];
		// 		echo $arrayprices[$i];
		// 		echo $arraytaxes[$i];
		// 		$subtotal[$i] = $arrayprices[$i] + ($arraytaxes[$i]*$arrayprices[$i])/100;
		// 		//$grandtotal = $grandtotal + $subtotal[$i];

		// }
		// //var_dump($allskus);
		// echo "up here";
		// echo $grandtotal;

		?>

		<html>

		 <body>
		 		<div class="logo">
		 			<img src="http://www.rekinza.com/images/logo-new.jpg" style="width:28%" />
		 		</div>
		 		<br/>
		 		<br/>
		   		<div class="rekinza-info">    
					M/s SCREVN ENTERPRISES LIMITED LIABILITY PARTNERSHIP<br/>
					43 Community Center<br/>
					New Friends Colony<br/>
					New Delhi-110065<br/>
					9810691177<br/>
					www.rekinza.com<br/>
					hello@rekinza.com<br/>
					TIN No. 07926957550<br/>
				</div>
				<div class="info">    
					First Name : <?php echo $firstname ?><br/>
					Last Name : <?php echo $lastname ?><br/>
					Email : <?php echo $email ?><br/>
					Phone Number: <?php echo $phone ?><br/>
					Payment Method: 
					<?php if ($payment_method == 'cashondelivery')
						{
							echo 'Cash Payment';
						}
						else
						{
							echo 'Card Payment';
						}
					?><br/>
					<br>
				</div>
				<table id="items_table">
					<tr>
						<th> Name: </th>
						<th> SKU: </th>
						<th> Price: </th>
						<th> Qty : </th>
						<th> Sales Tax : </th>
						<th> Subtotal: </th>
					</tr>   

					 <?php $totalsubtotal =0;
					  for ($i = 0; $i<$skucount; $i++ ) { 
							$totalsubtotal = $totalsubtotal + $arrayprices[$i];
						?>
					<tr id="t1">
					<td> <?php echo $arrayproductnames[$i] ?> </td>
					<td style="text-transform:uppercase;"> <?php echo $arrayskus[$i] ?> </td>
					<td> <?php echo "Rs. ".number_format($arrayprices[$i]) ?> </td>
					<td> <?php echo "1" ?> </td>
					<td> <?php echo $arraytaxes[$i]."%" ?> </td>
					<td> <?php echo "Rs. ".number_format($arraysubtotals[$i]) ?> </td>
					</tr>
					<?php 
					$discount = 0.1*$totalsubtotal;
					$discountedtotal = $totalsubtotal - $discount;
					$totaltax = $grandtotal - ($totalsubtotal - $discount);
					} ?>
				</table>
				<div class="totals">
					Subtotal: Rs. <input type = "number" name="subtotal" value = "<?php echo round($totalsubtotal) ?>"> 
					<br>
					Discount : Rs. <input type = "number" name="discount" value = "<?php echo round($discount) ?>">
					<br>
					Grand Total (excl. tax) : Rs. <input type = "number" name="tax" value = "<?php echo round($discountedtotal) ?>">
					<br>
					Sales Tax : Rs. <input type = "number" name="tax" value = "<?php echo round($totaltax) ?>">
					<br>
					Grand Total (incl. tax): Rs. <input type = "number" name="grandtotal" id = "grandtotal" value= "<?php echo round($grandtotal) ?>">
					<button onclick = "add_discount();">-</button>
					<div id = "discount_block" hidden>
						Additional Discount: Rs. <input type = "number" name="discount" id ="discount" onchange = "update_grand_total_after_discount();">
						<br>
						Grand Total (after discount): Rs. <input type = "number" name="gtwithdiscount" id = "gtwithdiscount">
					</div>
				</div>
					<!--div>
						<button onclick="emailinvoice()"> Email Invoice </button>
					</div-->
				<p>Please note : All sales are final. No Returns. No Exchange.
			</body>
		</html>
<?php

	} //panelif ends
	else
	{
		echo "not allowed for this email id";
	}
} //numrows ends
else 
{
	echo "Sorry! Not authorized.";
}

?>

<script>
function add_discount()
{
	if( document.getElementById("discount_block").hidden === true )
	{
		document.getElementById("discount_block").hidden = false;
	}
	else
	{
		document.getElementById("discount_block").hidden = true;
	}
}

function update_grand_total_after_discount()
{
	var discount_amount = document.getElementById("discount").value;
	var gt_with_discount = document.getElementById("grandtotal").value - discount_amount;
	document.getElementById("gtwithdiscount").value = gt_with_discount;
		
}


</script>
<style>
body
{
	background-color: #eaeaea;
    font-family: 'Avenir', sans-serif;
    width:88%;
    margin-left:auto;
    margin-right:auto;
}

h1 
{
    text-align: center;
    color: #ffffff;
    background-color: #50c7c2;
    margin-left:auto;
    margin-right:auto;
    font-family: 'Avenir', sans-serif;
}

span
{
    float:left;
    display:inline-block;
    font-family: 'Avenir', sans-serif;
}

h2
{
    text-align: center;
    color: #bb1515;
    background-color: #b7c3c2;
    margin-left:auto;
    margin-right:auto;
    font-family: 'Avenir', sans-serif;
}


div
{
    margin-left:auto;
    margin-right:auto;
    text-align:center;
    line-height:2em;
    font-family: 'Avenir', sans-serif;
    font-size: 12px;
}

legend
{
    font-size: 1em;
    background-color :#323232;
    color : white;
    width:80%;
    font-family: 'Avenir', sans-serif;

}

button
{
	color: white;
	background: #50c7c2;
	border: 2px outset #d7b9c9;
	font-size:1em;
	display:inherit;
    font-family: 'Avenir', sans-serif;
} 

table
{

    width:100%;
    text-align:center;
    line-height:2em;
    font-family: 'Avenir', sans-serif;
    border:1px solid #323232;
    margin: 20px 0;
    font-size: 12px;
}
.totals 
{
	text-align: right;
}

#discount_block
{
	text-align: right;
}

.rekinza-info
{
	text-align: left;
	display: inline-block;
	width:48%;
}
.info
{
	text-align: left;
	display: inline-block;
	width:48%;
}
p
{
	margin-left:auto;
    margin-right:auto;
    font-family: 'Avenir', sans-serif;
    font-size: 12px;
    font-weight: bold;
}
input
{
	border:none;
}


</style>