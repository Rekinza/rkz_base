<?php

set_time_limit(0);
ini_set('memory_limit', '1024M');
umask(0);

include 'db_config.php';
require_once '../../../app/Mage.php';
Mage::app();

if (($handle = fopen("category.csv", "r")) !== FALSE) {
  $counter = 1;
  while (($data = fgetcsv($handle)) !== FALSE) {
        if ($counter > 1) {
          $cateName = $data[0];
           echo $cateName."\n"; 
           $rootCateId = 20; // here we need to assign root cate Id under which you want to create your new category
          if($cateName){
           createCategory($cateName, $rootCateId);
          }
        }
        $counter++;
        if($counter > 2){  // if want to break the scrip
            //exit();
        }
    }
}
function createCategory($name, $rootCateId){

 $path = "1/2/5/".$rootCateId; // here 1/2 is my store root category path you can check this in database table 'catalog_category_entity' table
 $cateName = $name;
 $category = Mage::getModel('catalog/category');
 $category->setStoreId(0); // 0 = default/all store view. If you want to save data for a specific store view, replace 0 by Mage::app()->getStore()->getId().

 if ($id) { //if update
   //$category->load($id);
 }
  
 $general['name'] = $cateName;
 $general['path'] = $path; // catalog path
 //$general['description'] = " Category";
 //$general['meta_title'] = "My Category"; //Page title
 //$general['meta_keywords'] = "";
 //$general['meta_description'] = "";
 //$general['landing_page'] = ""; //has to be created in advance, here comes id
 $general['display_mode'] = "PRODUCTS"; //static block and the products are shown on the page
 $general['is_active'] = 0;
 $general['is_anchor'] = 1;
 //$general['page_layout'] = 'two_columns_left';
 //$general['url_key'] = "cars";//url to be used for this category's page by magento.
 //$general['image'] = "cars.jpg";

 $category->addData($general);
 try {
     $category->save();
     echo "Success! new category Id: ".$category->getId()."\n";
 }
 catch (Exception $e){
     echo $e->getMessage();
 }
}
?>