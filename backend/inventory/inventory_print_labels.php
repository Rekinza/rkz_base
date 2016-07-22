<?php
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

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
include 'db_config.php';

$query = "SELECT * FROM inventory WHERE creation_date BETWEEN '$start_date' AND '$end_date' and qc_status = 'accepted' ORDER BY brand ASC";

//$query = "SELECT * FROM inventory WHERE sku_name IN ('C-D-ASOS-36','C-B-AU-01', 'C-B-TEMT-01', 'C-B-MSN-09', 'C-B-TH-13', 'C-B-2XTZ-01','C-B-MDE-01')";
$result = mysql_query($query);

$numresult = mysql_numrows($result);

if ( $numresult > 0 )
{
	$i = 0;
	
	while ( $i < $numresult )
	{
		$sku_name = mysql_result($result,$i,'sku_name');
		$brand = mysql_result($result,$i,'brand');
		$product_name = mysql_result($result,$i,'product_name');
		$size = mysql_result($result,$i,'size');
		$retail_value = mysql_result($result,$i,'retail_value');
		
		$suggested_price = mysql_result($result,$i,'suggested_price');
		
		$discount = 0;
		if($retail_value >0)
		{
			$discount = ($retail_value - $suggested_price)/$retail_value;
			$discount = round($discount * 100,0);
			
			$discount = $discount."%";
		}
		
		$retail_value = "Rs.".$retail_value;
		$suggested_price = "Rs.".$suggested_price;
	
		?>
		<table>
		<tr>
			<td colspan="3" style="text-align:center;"><b> <?php echo $brand; ?></b></td>
		</tr>
		
		<tr>
			<td colspan="3" style="text-align:center;"> <?php
                                    if (strlen($product_name)>32){
                                    $product_name=substr($product_name,0,32);
                                    $product_name.=" ...";
                                    } 
                                    ?>
                                   <?php echo $product_name; ?>


				<!--?php echo $product_name; ? -->
			</td>
		</tr>
		
		<tr>
			<td colspan="3" style="text-align:center;"><b><?php echo $size; ?></b></td>
		</tr>
		
		<tr>
			<td style="text-align:right; width:33%;"><strike> <?php echo $retail_value ; ?></strike></td>
			<td style="text-align:center; width:33%;"> <?php echo $suggested_price ; ?></td>
			<td style="text-align:left; width:33%;"> <?php echo $discount ; ?></td>
		</tr>
		
		<tr>
			<td colspan="3" style="text-align:center;"> <?php echo $sku_name; ?></td>
		</tr>
		
		</table>
		
		<?php
		$i++;
	
	}

}

else
{
	echo "Nothing to print";
	
}
} //panelif ends
else {
	echo "not allowed for this email id";
}
} //numrows ends
else {
	echo "Sorry! Not authorized";
}
?>

<style>
tbody
{
	display:inline-table;
	width:100%;
}
tr 
{
	text-align:center;
	
}

table
{
	width:32%;
	margin-top:40px;
	display:inline-block;
	font-family: helvetica, sans-serif;
	border:none;
	font-size:11px;

}	

</style>