<?php
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
if(isset($_POST['mark_upload']))
{
	include 'db_config.php';
	include '../../app/Mage.php';
	Mage::init();
    $checkbox = $_POST['checkbox'];
	for($i=0;$i<count($checkbox);$i++){
		


	$mark_upload_id = $checkbox[$i];
	$query1 = "SELECT * FROM inventory WHERE sku_name ='$mark_upload_id' ";
	$result1 = mysql_query($query1);
	$numresult1 = mysql_numrows($result1);
	$sku_name1 = mysql_result($result1,0,'sku_name');
	echo($sku_name1);
	$customer_email_id1 = mysql_result($result1,0,'customer_email_id');
	$product_name1= mysql_result($result1,0,'product_name');
	$type1= mysql_result($result1,0,'type');
	$sub_type1= mysql_result($result1,0,'sub_type');
	$cat1 = mysql_result($result1,0,'category');
	$sub_cat1 = mysql_result($result1,0,'sub_category');
	$qc_status1= mysql_result($result1,0,'qc_status');
	$condition1= mysql_result($result1,0,'condition');
	$gently_used_comments1= mysql_result($result1,0,'gently_used_comments');
	//$product_name1=mysql_result($result1,0,'product_name');
	$brand1 = mysql_result($result1,0,'brand');
	$color1= mysql_result($result1,0,'color');
	$material1 = mysql_result($result1,0,'material');
	$measurements1 = mysql_result($result1,0,'measurements');
	$size1 = mysql_result($result1,0,'size');
	$retail_value1 = mysql_result($result1,0,'retail_value');
	$suggested_price1 = mysql_result($result1,0,'suggested_price');
	$special1 = mysql_result($result1,0,'special');
	$rejection_reason1 = mysql_result($result1,0,'rejection_reason');
	$upload_status1 = mysql_result($result1,0,'upload_status');
	$pickup_id1 = mysql_result($result1,0,'pickup_id');

	//starting
	//see if sku is not repeated	
	$id = Mage::getModel('catalog/product')->getIdBySku($sku_name1);
	if ($id){
	    echo "SKU {$sku_name1} exists";
	    continue;

	}
	else{
	    echo "Uploading...</br>";
	}

	//see if any of these values is empty
	if($product_name1 == "" || $brand1 == "" || $color1==""){
		echo "check product name, colour, or brand attribute break. Hence not uploaded. </br>";
		continue;
	}


	//logic for msrp	
	if($suggested_price1 >0 && $suggested_price1 < 750)
	{
		$msrp = $suggested_price1 - 200;
	}
	elseif ($suggested_price1 <= 5000) 
	{
		$msrp = 0.7*$suggested_price1;
	}
	elseif ($suggested_price1 <= 50000)
	{
		$msrp = 0.8*$suggested_price1;	
	}
	else
	{
		$msrp = 0.85*$suggested_price1;
	}

	$product_name1 = trim($product_name1);

	$meta_description = "Buy ".$brand1." ".$product_name1." Online in India.";
	$meta_description_lowercase = strtolower($meta_description);
	$meta_keywords = str_replace(" ", ",", $meta_description_lowercase);
	$meta_title = $brand1." ".$product_name1;
	$url_key = str_replace(" ", "-", $meta_title);
	$url_key_lower = strtolower($url_key);
	$url_path = $url_key_lower;


	$tax_class = 7;
	if($type1 == "Clothing")
	{
		if($suggested_price1 < 5000){
			$tax_class = 7;
		}
		elseif($suggested_price1 >= 5000){
			$tax_class = 5;
		}
	}
	else
	{
		if($suggested_price1 < 500){
			$tax_class = 7;
		}
		elseif($suggested_price1 >= 500){
			$tax_class = 5;
		}
	}
	$category = "";
	$subcategory = "";
	$sub2category = "";
	$sub3category = "";
//for setting category types:
	if($qc_status1 == "rejected")
	{
		$category = 103;
	}
	elseif($type1 == "Clothing"){
		$category = 3;
		if($sub_type1 == "Dress"){
			$subcategory = 8;
			if($cat1 == 'Bodycon Dress')
			{
				$sub2category = 40;
			}
			elseif($cat1 == 'Shift Dress')
			{
				$sub2category = 41;
			}
			elseif($cat1 == 'Wrap Dress')
			{
				$sub2category = 42;
			}
			elseif($cat1 == 'Flared Dress')
			{
				$sub2category = 43;
			}
			elseif($cat1 == 'A-Line Dress')
			{
				$sub2category = 44;
			}
			elseif($cat1 == 'Maxi Dress')
			{
				$sub2category = 45;
			}
			elseif($cat1 == 'Shirt Dress')
			{
				$sub2category = 46;
			}
			elseif($cat1 == 'Skater Dress')
			{
				$sub2category = 47;
			}
			elseif($cat1 == 'Peplum Dress')
			{
				$sub2category = 48;
			}
			elseif($cat1 == 'Sheath Dress')
			{
				$sub2category = 49;
			}
			elseif($cat1 == 'Tube Dress')
			{
				$sub2category = 104;
			}
		}
		elseif($sub_type1 == "Tops"){
			$subcategory = 9;

			if($cat1 == 'Tee')
			{
				$sub2category = 71;

				if($sub_cat1 == 'V Neck')
				{
					$sub3category = 77;
				}
				elseif($sub_cat1 == 'Round Neck')
				{
					$sub3category = 76;
				}
				elseif($sub_cat1 == 'Polo Neck')
				{
					$sub3category = 78;
				}
			}
			elseif($cat1 == 'Top')
			{
				$sub2category = 72;

				if($sub_cat1 == 'Crop')
				{
					$sub3category = 79;
				}
				elseif($sub_cat1 == 'Casual')
				{
					$sub3category = 80;
				}
				elseif($sub_cat1 == 'Formal')
				{
					$sub3category = 81;
				}
				elseif($sub_cat1 == 'One Shoulder')
				{
					$sub3category = 82;
				}
				elseif($sub_cat1 == 'Off Shoulder')
				{
					$sub3category = 83;
				}
				elseif($sub_cat1 == 'Cold Shoulder')
				{
					$sub3category = 84;
				}
				elseif($sub_cat1 == 'Peplum')
				{
					$sub3category = 85;
				}
				elseif($sub_cat1 == 'Tube')
				{
					$sub3category = 86;
				}
				elseif($sub_cat1 == 'Tank')
				{
					$sub3category = 87;
				}
				elseif($sub_cat1 == 'Halter')
				{
					$sub3category = 88;
				}
				elseif($sub_cat1 == 'Spaghetti')
				{
					$sub3category = 89;
				}
			}
			elseif($cat1 == 'Shirt')
			{
				$sub2category = 73;
			}
			elseif($cat1 == 'Tunic')
			{
				$sub2category = 74;
			}
		}
		elseif($sub_type1 == "Bottoms"){
			$subcategory = 10;

			if($cat1 == 'Jeans')
			{
				$sub2category = 58;
			}
			elseif($cat1 == 'Shorts')
			{
				$sub2category = 59;
			}
			elseif($cat1 == 'Skirt')
			{
				$sub2category = 60;
			}
			elseif($cat1 == 'Pants')
			{
				$sub2category = 61;
			}
			elseif($cat1 == 'Jeggings')
			{
				$sub2category = 62;
			}
		}
		elseif($sub_type1 == "Outerwear"){
			$subcategory = 11;

			if($cat1 == 'Jacket')
			{
				$sub2category = 63;
			}
			elseif($cat1 == 'Coat')
			{
				$sub2category = 64;
			}
			elseif($cat1 == 'Sweater')
			{
				$sub2category = 65;
			}
		}
		elseif($sub_type1 == "Ethnic"){
			$subcategory = 21;

			if($cat1 == 'Suit')
			{
				$sub2category = 66;
			}
			elseif($cat1 == 'Lehenga')
			{
				$sub2category = 67;
			}
			elseif($cat1 == 'Saree')
			{
				$sub2category = 68;
			}
			elseif($cat1 == 'Kurta')
			{
				$sub2category = 69;
			}
			elseif($cat1 == 'Kurti')
			{
				$sub2category = 70;
			}
		}
		elseif($sub_type1 == "Jumpsuit"){
			$subcategory = 75;
		}
	}
	elseif($type1 == "Shoes"){
		$category = 5;
		if($sub_type1 == "Flats"){
			$subcategory = 19;
		}
		elseif ($sub_type1 == "Heels") {
			$subcategory = 20;
		}
	}
	elseif($type1 == "Bags"){
		$category = 6;
		if($sub_type1 == "Wallet"){
			$subcategory = 13;
		}
		elseif($sub_type1 == "Clutch"){
			$subcategory = 25;
		}
		elseif($sub_type1 == "Handbag"){
			$subcategory = 12;
		}
		elseif($sub_type1 == "Luggage"){
			$subcategory = 28;
		}
	}
	elseif($type1 == "Accessories"){
		$category = 29;
		if($sub_type1 == "Belt"){
			$subcategory =30;
		}
		elseif($sub_type1 == "Scarf"){
			$subcategory =31;
		}
		elseif($sub_type1 == "Watch"){
			$subcategory =98;
		}
		elseif($sub_type1 == "Phone Case"){
			$subcategory =99;
		}
		elseif($sub_type1 == "Earrings"){
			$subcategory =100;
		}
		else{
			$subcategory = 32;
		}

	}



	if($type1 == "Clothing")
	{

			if($sub_type1 == "Outerwear" || $sub_type1 == "Tops" || $sub_type1 == "Dress")
			{
				$size_chart = "Women Tops Dresses";
			}
			elseif ($sub_type1 == "Bottoms") 
			{
				$size_chart == "Women Bottoms";
			}

	}
	elseif($type1 == "Shoes")
	{
		$size_chart = "Women Footwear";
	} 


//logic for short-description	
	if($qc_status1 == "accepted")
	{
		$status = 1;
			if($condition1 == "like new")
			{
				$shortdescription = "Like New - This item is in excellent condition, having been inspected thoroughly by Rekinza Quality Specialists. When it arrives at your door, you just might mistake it for brand new!";
			}
			elseif ($condition1 == "gently used") {
				$shortdescription = "Tiny Flaw - While in excellent condition, this item does indeed have a teeny, tiny flaw. ".$gently_used_comments1." ";
			}
			elseif ($condition1 == "new with tag") {
				$shortdescription = "New With Tag - Brand new! Never worn and still has the tags attached.";
			}

	}	
	elseif($qc_status1 == "rejected")
	{
		$status = 2;
		$shortdescription = "-";
	}


	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); //
    $product = Mage::getModel('catalog/product');
try{
    $product
    ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
    ->setAttributeSetId(4) // default attribute set
    ->setSku($sku_name1) // generate a random SKU
    ->setWebsiteIDs(array(1));

	$product
    ->setCategoryIds(array(2,$category,$subcategory,$sub2category,$sub3category))
    ->setStatus($status)
    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) // visible in catalog and search
    ->setUrlKey($url_key)
    ->setUrlPath($url_path)
    ->setMsrp($msrp);

	$product
      ->setName($product_name1) // add string attribute
      ->setShortDescription($shortdescription)
      ->setPrice($retail_value1)
      ->setSpecialPrice($suggested_price1)
      ->setTaxClassId($tax_class)    // Taxable Goods by default
      ->setWeight(0.3)
      ->setMetaTitle($meta_title)
      ->setMetaKeyword($meta_keywords)
      ->setMetaDescription($meta_description)
      ->setDescription($product_name1)
	  ->setReason($rejection_reason1)
	  ->setMaterial($material1)
	  ->setMeasurements($measurements1)
	  ->set
        ;

	 $arg_attribute = 'color';  
	 $arg_value = explode(",",$color1);
	 $val = array();
	 $j = 0;
	 foreach($arg_value as $individualvalue)
	 { 

	$attr = $product->getResource()->getAttribute("color");
	if ($attr->usesSource()) {
		$val[$j] = $attr->getSource()->getOptionId($individualvalue);
		if($val[$j] == ""){
			echo "color value not found. not updating the product. </br>";
			continue 2;
		}
		 $j++; 	 
		}
 
	}	
	$product->setColor($val);

	$attr = $product->getResource()->getAttribute("size_chart");
    if ($attr->usesSource()) {
    	$sizechart_id = $attr->getSource()->getOptionId($size_chart);
    }
        $product->setSizeChart($sizechart_id);    
        //echo "size chart: $sizechart_id </br>";

	
if($status!= 2){
	 $attr = $product->getResource()->getAttribute("size");
	if ($attr->usesSource()) {
	echo $size_id = $attr->getSource()->getOptionId($size1);
	}
		if($size_id)
		$product->setSize($size_id);
		else{
			echo "size id not found. check again. product not suploaded. </br>";
			continue;
			}
	}		

	$attr = $product->getResource()->getAttribute("brands");
	if ($attr->usesSource()) {
		$brand_id = $attr->getSource()->getOptionId($brand1);
	//echo "brands";
	}		


	if($brand_id){
		
		$product->setBrands($brand_id);
	}
	else{
		echo "brand not exists for the above sku. hence not uploaded </br>";
		continue; 
	}	


	$attr = $product->getResource()->getAttribute("vendor");
	if ($attr->usesSource()) {
	
	$vendor_id = $attr->getSource()->getOptionId($customer_email_id1);
	//echo "vendor";
	}
		
	if($vendor_id){
		//echo "<br> Code is here";
		$product->setVendor($vendor_id);
	}
	else{
		echo "This sku's vendor not found. Hence not updating </br>";
		continue;
	}

	if($status!=2){	
	 $attr = $product->getResource()->getAttribute("special");
		if ($attr->usesSource()) {
		echo $special_id = $attr->getSource()->getOptionId($condition1);
		}
	}

	else{
	$attr = $product->getResource()->getAttribute("special");
	if ($attr->usesSource()) {
	echo $special_id = $attr->getSource()->getOptionId("Like New");
	}	

	}
		$product->setSpecial($special_id);

	if($status!=2){
			$listOfImages = array();	
			$listOfImages[0] = "../../media/import/".$url_path."-3.jpg";
			$listOfImages[1] = "../../media/import/".$url_path."-2.jpg";
			$listOfImages[2] = "../../media/import/".$url_path."-1.jpg";
			//$count = 0;
			foreach ($listOfImages as $index=>$imagePath) {
			if(file_exists($imagePath))	
			{
			    try{	
			    	$mode = array();
			    	//if ($count == 0) {
			        $mode = array("thumbnail", "small_image", "image");
			    	//}
			    	$product->addImageToMediaGallery($imagePath, $mode, false, false);
				} catch (Exception $e) {
					echo $e->getMessage();
				}	
			}
			else if($index >0){
			echo "Can not find image by path: `{$imagePath}` hence not uploaded<br/>";
			continue 2;
	  		}
		}
	}	

	$product->save();

	//Set Product Qty and make it Available In Stock - Stuti
	$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku_name1); 
	//$product->setVisibility(1);
    //$product->getResource()->saveAttribute($product, 'visibility');
 
	 if ( $product ) {
	 
	 $productId = $product->getIdBySku($sku);
	 $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
	 $stockItemId = $stockItem->getId();
	 $stock = array();
	 
	 if (!$stockItemId) {
	 $stockItem->setData('product_id', $product->getId());
	 $stockItem->setData('stock_id', 1); 
	 } else {
	 $stock = $stockItem->getData();
	 }

	$stockItem->assignProduct($product);
	$stockItem->setData('is_in_stock', 1);
	$stockItem->setData('qty', 1);
	$product->setStockItem($stockItem);

	 $stockItem->save();

	 unset($stockItem);
	 unset($product);


	 }

	 //Qty Set Complete - Stuti


	$sql = "UPDATE inventory SET upload_status = 'uploaded' WHERE sku_name ='$mark_upload_id' ";
	$result = mysql_query($sql);

	}catch(Exception $e){
		echo "stuck on sku $sku_name1";
		echo $e->getMessage();
	}	
	} //end of for-loop

		
		if($result =='TRUE')
		{
			echo "Marked uploaded<br>";
		}

	mysql_close();
}



include 'db_config.php';
$query = "SELECT * FROM inventory WHERE upload_status != 'uploaded' AND ( (qc_status = 'accepted' AND size != '') OR (qc_status ='rejected')) ORDER BY qc_status ASC, creation_date ASC";
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
		<button> CHECKTOSEE </button>
		
		<table id="report_table">
			<th>S. No</th>
			<th><input type ="checkbox" onchange ="checkAll(this)" name ="chk">Select All</th>
			<th>SKU Code</th>
			<th>Creation Date</th>
			<th>Pickup Id</th>
			<th>Email ID</th>
			<th>Product Name</th>
			<th>Type</th>
			<th>Sub Type</th>
			<th>QC Status</th>
			<th>Condition</th>
			<th>Gently Used Comments</th>
			<th>Brand</th>
			<th>Color</th>
			<th>Material</th>
			<th>Measurements</th>
			<th>Size</th>
			<th>Retail Value</th>
			<th>Suggested Price</th>
			<th>Special</th>
			<th>Rejection Reason</th>
			<th>Upload Status</th>

			<form action = 'inventory_pending_upload.php' method = 'POST'>
	<?php
	$i = 0;
	while ( $i < $numresult )
	{
			$sku_name = mysql_result($result,$i,'sku_name');
			$creation_date = mysql_result($result,$i,'creation_date');
			$customer_email_id = mysql_result($result,$i,'customer_email_id');
			$product_name= mysql_result($result,$i,'product_name');
			$type= mysql_result($result,$i,'type');
			$sub_type= mysql_result($result,$i,'sub_type');
			$qc_status= mysql_result($result,$i,'qc_status');
			$condition= mysql_result($result,$i,'condition');
			$gently_used_comments= mysql_result($result,$i,'gently_used_comments');
			$brand = mysql_result($result,$i,'brand');
			$color= mysql_result($result,$i,'color');
			$material = mysql_result($result,$i,'material');
			$measurements = mysql_result($result,$i,'measurements');
			$size = mysql_result($result,$i,'size');
			$retail_value = mysql_result($result,$i,'retail_value');
			$suggested_price = mysql_result($result,$i,'suggested_price');
			$special = mysql_result($result,$i,'special');
			$rejection_reason = mysql_result($result,$i,'rejection_reason');
			$upload_status = mysql_result($result,$i,'upload_status');
			$pickup_id = mysql_result($result,$i,'pickup_id');
		
		?>
		
		

		<tr>
			<td> <?php echo $i+1 ?></td>
			<td><input type = "checkbox" name = "checkbox[]" class="chkbox" value = "<?php echo $sku_name?>"> </td>
			<td><?php echo $sku_name; ?></td>
			<td><?php echo$creation_date ?></td>
			<?php
			if($prev_pickup_id == $pickup_id)
			{
				$pickup_status = $prev_pickup_status;
			}
			else
			{
			$query2 = "SELECT * FROM thredshare_pickup WHERE id = $pickup_id";
			$result2 = mysql_query($query2);
			$j = 0;
			$pickup_status = mysql_result($result2,$j,'status');
			}

			if($pickup_status == "priced" || $pickup_status == "live")
			{
				?>
				<td style="background-color:#50c7c2;"><?php echo $pickup_id;?></td>
				<?php
			}
			else
			{
				?>
				<td><?php echo $pickup_id;?></td>
				<?php
			}	
			$prev_pickup_id = $pickup_id;
			$prev_pickup_status = $pickup_status;
			?>
			<td><?php echo $customer_email_id ;?></td>
			<td ><?php echo $product_name;?></td>
			<td ><?php echo $type;?></td>
			<td ><?php echo $sub_type;?></td>
			<td ><?php echo $qc_status;?></td>
			<td ><?php echo $condition;?></td>
			<td ><?php echo $gently_used_comments;?></td>
			<td><?php echo $brand; ?></td>
			<td><?php echo $color; ?></td>
			<td><?php echo $material?></td>
 			<td><?php echo $measurements?></td>
			<td><?php echo $size ?></td>
			<td><?php echo $retail_value ?></td>
			<td><?php echo $suggested_price ?></td>
			<td><?php echo $special ?></td>
			<td><?php echo $rejection_reason ?></td>
			<td><?php echo $upload_status ?></td>
			
		</tr>
						
	<?php
	$i++;
	}
	
?>
	</table>
	<p class ="submit">
	<input type = "Submit" id = "mark_upload" name ="mark_upload" value = "Mark uploaded">
	</p>
	</form>
	<br><br>
	
	
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

button
{
		width:20%;
}

p{
	
	text-align:center;
}
.submit input
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
    for(j = 0 ; j < tab.rows.length ; j++) 
    {   
        tab_text=tab_text+tab.rows[j].innerHTML;
        tab_text=tab_text+"</tr><tr>";
    }

    tab_text = tab_text+"</tr></table>";

	var txt = new Blob([tab_text], {type: "text/plain;charset=utf-8"});
	saveAs(txt,"SKUs_For_Upload.xls");
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
 

/* Code to enable ctrl and shift key selection on check box  */ 
var lastChecked = null;
    
$(document).ready(function() {
	var $chkboxes = $('.chkbox');
	$chkboxes.click(function(e) {
		if(!lastChecked) {
			lastChecked = this;
			return;
		}

		if(e.shiftKey) {
			var start = $chkboxes.index(this);
			var end = $chkboxes.index(lastChecked);

			$chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);

		}

		lastChecked = this;
	});
});
 
</script>