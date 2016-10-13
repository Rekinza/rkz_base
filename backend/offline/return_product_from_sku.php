<?php
require_once '../../app/Mage.php';
include '../db_config.php';
Mage::app();

$sku = $_POST['sku'];
$flag = "not in stock";
//  mage::log("in returns hsowing posted data");
// mage::log($sku);
$product = Mage::getModel('catalog/product');
$id = Mage::getModel('catalog/product')->getResource()->getIdBySku($sku);
if ($id) {
    $product->load($id);

    $imgurl = $product->getImageUrl();
    $price = $product->getSpecialPrice();
    $productname = $product->getName();

    //for tax:
    $taxClassId = $product->getData("tax_class_id");
    $taxClasses = Mage::helper("core")->jsonDecode(Mage::helper("tax")->getAllRatesByProductClass());
    $taxRate = $taxClasses["value_".$taxClassId];
    
    //for stock availability:
    if ($product->getStockItem()->getIsInStock())
    {
        $flag = "in stock";
    }
    else {
        $flag = "WARNING: NOT IN STOCK";
    }
 
        echo json_encode(
        array("image" => $imgurl,
            "productname" => urlencode($productname),
			"price" => $price,
			"tax" => $taxRate,
			"flag" => $flag)
			);
}
else{

    echo FALSE;
}
?>