<?php

include 'db_config.php';
include_once("../../login-with-google-using-php/config.php");
include_once("../../login-with-google-using-php/includes/functions.php");
include '../utils/access_block.php';

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
		
	$customer_email_id = $_GET['customer_email_id']; 
	$pickup_id = $_GET['pickup_id'];

	if($customer_email_id)
	{
		$query = "SELECT * FROM `inventory` WHERE customer_email_id = '".$customer_email_id."'" ;
	}

	else
	{
		$query = "SELECT * FROM `inventory` WHERE pickup_id = '".$pickup_id."'" ;
	}

	$result = mysql_query($query);

	$numresult = mysql_numrows($result);

	if ( $numresult > 0 )
	{
		
		?>
		<head>
			<script src="jquery-1.11.1.js"></script>
			<script src="FileSaver.js"></script>
		<head>
		
		<body>
		<div id ="inventory_table">
			<h1>Inventory Details</h1>
			<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>
			
			<button id="btnExport" onclick="fnExcelReport();" > Export To Excel </button>
		
			<table id="report_table">
				<th>S. No</th>
				<th>SKU Code</th>
				<th>Name</th>
				<th>Brand</th>
				<th>Color</th>
				<th>QC Status </th>
				<th>Condition </th>
				<th>Gently Used comments </th>	
				<th>Size</th>
				<th>Special</th>
				<th>Rejection Reason</th>
				<th>Maybe Reason</th>
				<th>Upload Status</th>
				<th>Order ID</th>
				<th>Status</th>
				<th>MSRP</th>
				<th>Update</th>
		<?php
		$i = 0;
		while ( $i < $numresult )
		{
				$sku_name = mysql_result($result,$i,'sku_name');
				$customer_email_id = mysql_result($result,$i,'customer_email_id');
				$product_name= mysql_result($result,$i,'product_name');
				$brand = mysql_result($result,$i,'brand');
				$color= mysql_result($result,$i,'color');
				$qc_status= mysql_result($result,$i,'qc_status');
				$condition = mysql_result($result,$i,'condition');
				$gently_used_comments = mysql_result($result,$i,'gently_used_comments');	
				
				$size = mysql_result($result,$i,'size');
				$special = mysql_result($result,$i,'special');
				$rejection_reason = mysql_result($result,$i,'rejection_reason');
				$maybe_reason = mysql_result($result,$i,'maybe_reason');
				$upload_status = mysql_result($result,$i,'upload_status');
			
				
				/*******Get Order ID of the sold inventory***************/
				$query = "SELECT * FROM `inventory` WHERE customer_email_id = '".$customer_email_id."'" ;
				
				$q = "SELECT o.increment_id , o.status 
				FROM sales_flat_order o 
				INNER JOIN sales_flat_order_item oi ON oi.order_id = o.entity_id
				WHERE oi.sku='".$sku_name."' 
				ORDER BY o.increment_id DESC";

				$res = mysql_query($q);
				$order_id = mysql_result($res,0,'increment_id');
				$order_status = mysql_result($res,0,'status');
				
				/***************************************/
				
				$_Pdetails = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku_name);
				
				if ($_Pdetails)
				{
					$msrp = $_Pdetails->getMsrp();
				}
				else
				{
					$msrp = 'N_A';
				}		
			?>
			
			

			<tr>
				<td> <?php echo $i+1 ?></td>
				<form action = "inventory_search_by_filter.php" method = "GET">
				<td><textarea name="sku_name" style ="width:50px;" readonly = "true"><?php echo $sku_name; ?></textarea></td>
				<td ><?php echo $product_name;?></td>
				<td><?php echo $brand; ?></td>
				<td><?php echo $color; ?></td>
				<td><?php echo $qc_status ?></td>
				<td><?php echo $condition ?></td>
				<td><?php echo $gently_used_comments ?></td>
					
				<td><?php echo $size ?>
				</td>
				<td><?php echo $special?></td>
				<td><?php echo $rejection_reason?></td>
				<td><?php echo $maybe_reason?></td>
				
				<td><?php echo $upload_status ?></td>
				<td><?php echo $order_id; ?></td>
				<td><?php echo $order_status; ?></td>
				<td><?php echo $msrp; ?></td>
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
	saveAs(txt,"Seller_Inventory.xls");
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

/********************* Suggested price autofill based on retail value*********************/
function FillSuggPrice(value) 
{
	/*
    var retail_price = document.getElementById('retail_value');
	console.log(value);
    document.getElementById('suggested_price').value=0.3 * retail_price.value;
      */  
}


</script>