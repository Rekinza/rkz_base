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
   
	$email_id_of_order = $order->getCustomerEmail();
		
	if( strcasecmp($email_id_of_order, $email_id) ==0)
	{
		//echo "Sorry ! The order was not placed from this email ID";
    	if($order_status != "really_confirmed") 	
		{	
			$product_skus = array();
			$product_names  = array();
			$product_urls = array();
			$items = $order->getAllItems();
			if ($order_status  == 'return' )
			{
				
				include 'db_config.php';
				$query = "SELECT status, items, pickup_date from thredshare_returns where order_id = '$order_number' ORDER BY id DESC LIMIT 1";
				$result = mysql_query($query);
				//$result_set = mysqli_fetch_all($result,MYSQLI_ASSOC);
				$numresult = mysql_numrows($result);
				if ($numresult > 0)
				{
					$marked_items = array();
					for($i = 0; $i < $numresult; $i++)
					{
						$marked_items_array = explode(", ", mysql_result($result, 0,'items'));
						foreach($marked_items_array as $marked_item)
						{	
							array_push($marked_items, $marked_item);
						}
					
					}
					foreach($items as $i)
					{
						$singlesku = $i->getSku();
						array_push($product_skus, $singlesku);
					}
						
					$count = count($product_skus);
					$rem_skus = array_values(array_diff($product_skus, $marked_items));	//because of shortcoming-diff
					$product_skus = $rem_skus;
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
						
						}
						$return_pickup_date = mysql_result($result,0,'pickup_date');
						$status = mysql_result($result,0,'status');
						$today = date("Y-m-d");
						if($status == "requested" || ($status == "scheduled" and ($today < $return_pickup_date)))
						{
						$message = "Your return requests will be merged and done on '$return_pickup_date', if you wish to modify an existing return request, kindly email us at blahblah";
						}
						else
						{
							$message = "You have a new return pickup to be scheduled";
						}
					
						echo json_encode( array("count" => $count_rem_skus,
						"product_skus" => $rem_skus,
						"product_brands" => $product_brands,
						"product_names" => $product_names,
						"product_imgs" => $product_urls,
						"message" => $message));
					}
				}	
				else
				{
					//do nothing
					echo "the order is in return state without products";
				}	
			}
			else if( ($order_status == 'complete') || ($order_status == 'in_transit') || ($order_status == 'delivered') || ($order_status == 'processing') || ($order_status == 'undelivered'))
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
				"product_imgs" => $product_urls,
				"message" => ""));
			}
			
			else
			{
				echo 'Sorry ! You cannot place a return request for this order';
			}
		//}
		}//for really_confirmed
		else
		{
			echo "Sorry! Your 3 day return period has lapsed";
		}
		//Do nothing;
	}  
	else{
		echo "Sorry! Email id and order dont match"; 
	}  
}
else
{
	echo "Please enter a valid Order ID";
}
?>
