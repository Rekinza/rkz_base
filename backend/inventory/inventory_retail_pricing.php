<?php
include '../../app/Mage.php';
Mage::app();
include 'db_config.php';

$sku = $_POST['sku'];
//mage::log($sku);

$query1 = "SELECT * from inventory where sku_name = '$sku'";
$result1 = mysql_query($query1);

$product_name = mysql_result($result1,0,'product_name');
$brand = mysql_result($result1,0,'brand');
$material = mysql_result($result1,0,'material');
$type = mysql_result($result1,0,'type');
$subtype = mysql_result($result1,0,'sub_type');

$product_name_array = explode(" ", $product_name);
$product_keyword = array();
$product_query = "";
foreach($product_name_array as $pr)
{
	$q = "SELECT * from product_name_keywords where name = '$pr' ";
	$r = mysql_query($q);
	if(mysql_numrows($r))
	{
		//array_push($product_keyword, $pr);
		$product_query .= "and product_name LIKE '%$pr%'";
	}
	else{
		continue;
	}
}


if ((strpos($material, 'Secondary') == true) && (strpos($material, 'rimary') == true)) 
{
	$word1 = 'Primary Material: ';
	$word2 = 'Secondary';
	preg_match('/'.preg_quote($word1).'(.*?)'.preg_quote($word2).'/is', $material, $match);
	$material_string = $match[1];

}

elseif(strpos($material, 'Secondary') == false && strpos($material, 'rimary') != false)
{
	$match = explode(": ", $material);
	$material_string = $match[1];
}

else
{
	//mage::log("error");
	echo "error :  Material Missing";
	exit(0);	
}


//query building depending on material count:

if(strpos($material_string, ',') != false)
{
	$material_array1 = explode(", ", $material_string);
	$temp = $material_array1[0];
	$material_array1[0] = $material_array1[1];
	$material_array1[1] = $temp;
	$material_array2 = implode(",", $material_array1);

	$material_query = "(material LIKE 'Primary Material: $material_string%' OR material LIKE '$material_string' OR material LIKE 'Primary Material: $material_array2%'  OR material LIKE '$material_array2')" ;

	$query2 =  "Select * from inventory where brand = '$brand' and $material_query $product_query and type = '$type' and sub_type = '$subtype'  and sku_name != '$sku' and qc_status = 'accepted' ORDER BY creation_date LIMIT 24";
}

else
{
	trim($material_string);

	$material_query = "(material LIKE 'Primary Material: $material_string%' OR material LIKE '______$material_string%' OR material LIKE '_____$material_string%' OR material LIKE '____$material_string%' OR material LIKE '___$material_string%' OR material LIKE '__$material_string%' OR material LIKE '_$material_string%' OR material LIKE '$material_string%')" ;

	$query2 =  "Select * from inventory where brand = '$brand' and $material_query $product_query and type = '$type' and sub_type = '$subtype' and sku_name != '$sku' and qc_status = 'accepted' ORDER BY creation_date LIMIT 24";
}

$result2 = mysql_query($query2);
$count = mysql_numrows($result2);
//$result_set2 = mysqli_fetch_all($result2, MYSQLI_ASSOC);
$sku_names = array();

for($i = 0; $i<$count; $i++)
{
	$sku_name2 = mysql_result($result2,$i,'sku_name');
	$product_name2= mysql_result($result2,$i,'product_name');
	$brand2 = mysql_result($result2,$i,'brand');
	$material2 = mysql_result($result2,$i,'material');
	$creation_date = mysql_result($result2,$i,'creation_date');

	//mage::log($product_name2." "."$type2 $subtype2"." ".$material2);
	array_push($sku_names, $sku_name2);	//creating an array of skus

}
//mage::log($sku_names);


//get products
$image_array = array();
$price_array = array();
$productname_array = array();
$brand_array = array();
$creation_date_array = array();
$count = 0;
if(count($sku_names))
{
	foreach($sku_names as $sku)
	{	

		$product = Mage::getModel('catalog/product');
		$id = Mage::getModel('catalog/product')->getResource()->getIdBySku($sku);
		if ($id)
		{
			mage::log("ID found");
		    $product->load($id);

		    $imgurl = $product->getImageUrl();
		    $price = $product->getPrice();
		    $productname = $product->getName();
			$brand = $product->getResource()->getAttribute('brands')->getFrontend()->getValue($product);
		   // mage::log($productname);

		    array_push($image_array, $imgurl);
		    array_push($price_array, $price);
		    array_push($productname_array, $productname);	
			array_push($brand_array, $brand);
			array_push($creation_date_array, $creation_date);			
			
		    $count++;	 
		}
		else
		{
		    //echo FALSE;
		}
	}	
	
	if ($count==0)
	{
		echo "no items";
	}

	//mage::log($image_array);
	//mage::log($price_array);
	//mage::log($productname_array);

	echo json_encode(array(
		"count" => $count,
		"productnames" => $productname_array,
		"prices" => $price_array, 
		"images" => $image_array,
		"brands" => $brand_array
		));	

}

else
{
	//mage::log("no items");
	echo "no items";
}



?>