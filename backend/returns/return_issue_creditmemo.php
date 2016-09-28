<?php
include '../db_config.php';
include '../../app/Mage.php';
Mage::app();

	$id = $_POST['id'];
		//updating in magento
		$query2 = "SELECT order_id, items, email from thredshare_returns where id = '$id' ";
		$result2 = mysql_query($query2);
		$numresult = mysql_num_rows($result2);
		if($numresult > 0)
		{
			$order_id = mysql_result($result2,0,'order_id');
			$email_id = mysql_result($result2,0,'email');
			echo $order_id;
			$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);

			//check if credit memo can be made for the particular order
	        if (!$order->canCreditmemo()) {
	            echo "cannot_create_creditmemo: check if already made or if not shipped/invoiced";
	            exit(0);
	        }
	        

			$items = mysql_result($result2,0,'items');
			$return_skus = explode(", ", $items);

			//setting quantity to one for every product
			$qtys = array();
			foreach($return_skus as $item_sku)
			{
				$orderItem = $order->getItemsCollection()->getItemByColumnValue('sku', $item_sku);
				$orderItemId = $orderItem->getId();
				$qtys[$orderItemId] = 1;

				$service = Mage::getModel('sales/service_order', $order);
				$data['qtys'] = $qtys;

				//Set product qty
				$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item_sku); 
			 
			 	if($product) 
			 	{
				 
					 $productId = $product->getIdBySku($item_sku);
					 $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
					 $stockItemId = $stockItem->getId();
					 $stock = array();
				 
				 if (!$stockItemId) 
				 {
					 $stockItem->setData('product_id', $product->getId());
					 $stockItem->setData('stock_id', 1); 
				 } 
				 else 
				 {
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

				else
				{
					echo "Product by sku $item_sku is not found";
					exit(0);
				}

			} //foreach ends


			$creditmemo = $service->prepareCreditmemo($data)->register()->save(); 
			$order->save();



	        $statusname = $order->getStatusLabel();
	        if($statusname == "Closed")
	        {
	        	$order->setData(Mage_Sales_Model_Order::STATE_CLOSED);
				$order->setStatus("closed_return");
				$order->addStatusHistoryComment("Closed return");
	        }

			$order->save();


			//now to get the points which are to be refunded
			$creditnotes = $order->getCreditmemosCollection();
            foreach ($creditnotes as $creditnote)
            {
	            $rewardpointsnew = $creditnote->getGrandTotal();
			}

			$customer = Mage::getModel("customer/customer");
			$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
			$customer->loadByEmail($email_id);
			$customer_id = $customer->getId();

			$customer_group_id = Mage::getModel('customer/customer')->load($customer_id)->getGroupId();
    		$store_id = Mage::app()->getStore()->getId();
	       
			$resultsforrewards = Mage::getModel('rewardpoints/activerules')->getResultActiveRulesExpiredPoints($type_of_transaction,$customer_group_id,$store_id);
			$rewardpoints = $resultsforrewards[0];
		 	$expired_day = $resultsforrewards[1];
			$expired_time = $resultsforrewards[2];
		 	$point_remaining = $resultsforrewards[3];
		 	//test this line:
    		Mage::helper('rewardpoints/data')->checkAndInsertCustomerId($customer_id, 0);
			$_customer = Mage::getModel('rewardpoints/customer')->load($customer_id);
		

			$_customer->addRewardPoint($rewardpointsnew);
			$rewardpointsfinal = $rewardpointsnew + $rewardpoints;
			$details = "Refund for Order Number: ".$order_id;
			$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::ADMIN_ADDITION, 
									 'amount'=>(int)$rewardpointsnew, 
									 'balance'=>$_customer->getMwRewardPoint(), 
									 'transaction_detail'=>$details, 
									 'transaction_time'=>now(), 
									 'expired_day'=>$expired_day,
						    		 'expired_time'=>$expired_time,
						    		 'point_remaining'=>$point_remaining,
									 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
				$_customer->saveTransactionHistory($historyData);

				$querynew = "UPDATE thredshare_returns SET status = 'refunded' WHERE id = '$id' ";
				$resultnew = mysql_query($querynew);

				if ($resultnew == 'TRUE')
				{
					echo "DONE DONE DONE";
				}
				else
				{
					echo "creditmemo made, panel not updated. Please check";
				}



				

				echo "over";


		} //if ends