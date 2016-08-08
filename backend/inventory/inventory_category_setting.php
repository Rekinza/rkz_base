<?php
include 'db_config.php';
include '../../app/Mage.php';
Mage::app();
$_productCollection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->setCurPage(1)
                        ->setPageSize(100)
                        ->addAttributeToSelect('*')
					     ->joinField('qty',
					                 'cataloginventory/stock_item',
					                 'qty',
					                 'product_id=entity_id',
					                 '{{table}}.stock_id=1',
					                 'left')
					     ->addAttributeToFilter('qty', array("eq" => 1))
					     ->addAttributeToFilter('status',
    											array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                        ->load();

$count = $_productCollection->getSize();
echo "count: ".$count;                        


foreach ($_productCollection as $_product){
   $suffix = '';
	$sku = $_product->getSku();
   $query = "SELECT sub_type, category, sub_category from inventory where sku_name  = '$sku'"; 
   $result = mysql_query($query);
   $sub_type = mysql_result($result, 0, 'sub_type');
   $category = mysql_result($result, 0, 'category');
   $sub_category = mysql_result($result, 0, 'sub_category');
   echo $_product->getId().'</br>';
   echo $_product->getName().'</br>';
   echo $sku.'</br>';


   if($sub_category != NULL)
   {
      echo $sub_category.'</br>';
      $query2 = "SELECT suffix from subcat_mapping where name = '$sub_category'";
      $result2 = mysql_query($query2);
      $suffix = mysql_result($result2, 0, 'suffix');
      $suffix_array = explode(", ", $suffix);
      echo $suffix.'</br>';
      $categoryIds = $_product->getCategoryIds();
      var_dump($categoryIds);
      $_product ->setCategoryIds($suffix_array);
      $_product->save();
   }
   elseif ($category != NULL) 
   {
      echo $category.'</br>';  
      $query2 = "SELECT suffix from category_mapping where name = '$category'";
      $result2 = mysql_query($query2);
      $suffix = mysql_result($result2, 0, 'suffix');
      $suffix_array = explode(", ", $suffix);
      echo $suffix.'</br>';
      $categoryIds = $_product->getCategoryIds();
      var_dump($categoryIds);
      $_product ->setCategoryIds($suffix_array);
      $_product->save();
   }
   elseif ($sub_type != NULL) 
   {
      echo $sub_type.'</br>';
      $query2 = "SELECT suffix from subtype_mapping where name = '$sub_type'";
      $result2 = mysql_query($query2);
      $suffix = mysql_result($result2, 0, 'suffix');
      $suffix_array = explode(", ", $suffix);
      echo $suffix.'</br>';
      $categoryIds = $_product->getCategoryIds();
      var_dump($categoryIds);
      $_product ->setCategoryIds($suffix_array);
      $_product->save();
   }


   }
?>