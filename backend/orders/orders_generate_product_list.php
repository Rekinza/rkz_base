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
if($numresult > 0)
{
	$email = mysql_result($result,0,'email');
	$access_level = mysql_result($result,0,'access_level');
	$blockname = "order panel";
	$panel_access = ac_level($blockname);
	if($access_level <= $panel_access)
	{
	Mage::app();

	$checkbox = $_POST['checkbox'];

	$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'brands');
	$id = $attribute->getId();	

	$storeId = Mage::app()->getStore()->getId();

	$products_array = array();	

	$index = 0;

	for($i=0;$i<count($checkbox);$i++)
	{	
		$order_id = $checkbox[$i];
		
		$order = Mage::getModel('sales/order')->load($order_id);
			
		$increment_id = $order->getIncrementId();
			
		$order_items = $order->getAllVisibleItems();
		
		foreach($order_items as $sItem) 
		{
			$image = Mage::helper('catalog/image')->init($sItem->getProduct(), 'thumbnail')->resize(185, 256);
			
			$products_array[$index]['order_id'] = $increment_id;
			$products_array[$index]['item_sku'] = $sItem->getSku();
			$products_array[$index]['item_name'] = $sItem->getName();
			$products_array[$index]['product_id'] = $sItem->getProductId();
			
			
			$attribute_option_id = Mage::getResourceModel('catalog/product')->getAttributeRawValue($sItem->getProductId(), 'brands', $storeId);
			$product = Mage::getModel('catalog/product')->setStoreId($storeId)->setData('brands', $attribute_option_id);//the result from above

			$products_array[$index]['brand'] = $product->getAttributeText('brands');

			$attribute_option_id = Mage::getResourceModel('catalog/product')->getAttributeRawValue($sItem->getProductId(), 'size', $storeId);
			$product = Mage::getModel('catalog/product')->setStoreId($storeId)->setData('size', $attribute_option_id);//the result from above
			
			$products_array[$index]['size'] = $product->getAttributeText('size');
			
			$index++;

		}
			
	}

	aasort($products_array,"brand");	


	?>
	<table>
	<h1>Products in orders</h1>
	<a href = "../../login-with-google-using-php/logout.php"><button class ="panel_button">Logout</button></a>

	<th>S.no</th>
	<th>Order ID</th>
	<th>Brand</th>
	<th>SKU</th>
	<th>Product Name</th>
	<th>Size</th>
	<th>Price</th>
	<th>Image</th>

<?php
$i=0;
foreach($products_array as $single_array)
{
	$_product = Mage::getSingleton('catalog/product')->load($single_array['product_id']);
	$i++;
	?>
	<tr>
	<td><?php echo $i ;?></td>
	<td><?php echo $single_array['order_id'];?></td>
	<td><?php echo $single_array['brand'];?></td>
	<td><?php echo $single_array['item_sku']; ?></td>
	<td><?php echo $single_array['item_name']; ?></td>
	<td><?php echo $single_array['size']; ?></td>
	<td><?php echo $_product->getSpecialPrice(); ?></td>
	<td><img src="<?php echo Mage::helper('catalog/image')->init($_product, 'thumbnail')->resize(185, 256); ?>"/></td>
	</tr>
	
	<?php
	
}
?>
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


function aasort (&$array, $key) {
    $sorter=array();
    $ret=array();
    reset($array);
    foreach ($array as $ii => $va) {
        $sorter[$ii]=$va[$key];
    }
    asort($sorter);
    foreach ($sorter as $ii => $va) {
        $ret[$ii]=$array[$ii];
    }
    $array=$ret;
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