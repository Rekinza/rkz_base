<?php

include '../../app/Mage.php';

Mage::app();

$order_number =$_POST['order_number'];
$email_id =$_POST['email_id'];

$collection = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('increment_id', $order_number);

if ($collection->count())   //Check if order ID entered is valid
{
	
    $order = Mage::getModel('sales/order')->loadByIncrementId($order_number);
   
    $order_status = $order->getStatus();
   

    if($order_status != "really_confirmed") 	
	{	
 
	    $email_id_of_order = $order->getCustomerEmail();
		
		if( strcasecmp($email_id_of_order, $email_id) !=0)
		{
			echo "Sorry ! The order was not placed from this email ID";
		}
		else
		{	

			$product_skus = array();
			$product_names  = array();
			$product_urls = array();
			$items = $order->getAllItems();


			if ($order_status  == 'return' )
			{
				
				include 'db_config.php';
				$query = "SELECT items from thredshare_returns where order_id = '$order_number'";
				$result = mysql_query($query);
				
				$numresult = mysql_numrows($result);

				if ($numresult > 0)
				{

					$marked_items = array();
					for($i = 0; $i < $numresult; $i++)
					{

						$marked_items_array = explode(", ", mysql_result($result,$i,'items')); 
						
						foreach($marked_items_array as $marked_item)
						{	
						array_push($marked_items, $marked_item);
						}
					
					}

					foreach($items as $i){

							$singlesku = $i->getSku();
							array_push($product_skus, $singlesku);
					}

						
					$count = count($product_skus);
					$rem_skus = array_diff($product_skus, $marked_items);
					$product_skus = array();
					$count_rem_skus = count($rem_skus);

					if($count_rem_skus == 0)
					{
						echo "Sorry, return process has been initiated for all items in this order";
						exit(0);
					}
					else
					{

						$product_brands = array();

						foreach ($rem_skus as $rem_sku) 
						{
 
							$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$rem_sku);

							$brand = $product->getResource()->getAttribute('brands')->getFrontend()->getValue($product);
							$imgurl = $product->getImageUrl();
							
							$product_name = $product->getName();

							array_push($product_brands, $brand);
							array_push($product_names, $product_name);
							array_push($product_urls, $imgurl);
							array_push($product_skus, $rem_sku);

						
						}

					
						echo json_encode( array("count" => $count_rem_skus,
						"product_skus" => $product_skus,
						"product_brands" => $product_brands,
						"product_names" => $product_names,
						"product_imgs" => $product_urls));
					}
				}	
				else
				{
					//do nothing
				}	

			}


			else if( ($order_status == 'complete') || ($order_status == 'in_transit') || ($order_status == 'delivered') || ($order_status == 'processing') || ($order_status == 'undelivered') )
			{
					$product_brands = array();
					$product_skus = array();
					$count = count($items);
					foreach ($items as $i) {

						$singlesku = $i->getSku();
						$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$singlesku);

						$brand = $product->getResource()->getAttribute('brands')->getFrontend()->getValue($product);
						$imgurl = $product->getImageUrl();

						array_push($product_brands, $brand);
						array_push($product_names, $i->getName());
						array_push($product_urls, $imgurl);
						array_push($product_skus, $singlesku);
					
					}				
				echo json_encode( array("count" => $count,
				"product_skus" => $product_skus,
				"product_brands" => $product_brands,
				"product_names" => $product_names,
				"product_imgs" => $product_urls));
			}
			
			else
			{
				echo 'Sorry ! You cannot place a return request for this order';
			}
		}
	//Do nothing;
	}  
	else{
		echo "Sorry! Your 3 day return period has lapsed"; 

	}  
}
else
{
	echo "Please enter a valid Order ID";
}

?>