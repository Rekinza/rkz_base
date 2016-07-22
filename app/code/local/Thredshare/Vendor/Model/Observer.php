<?php
class Thredshare_Vendor_Model_Observer{

    public function setStatus() {
        Mage::log("New!");
        $startdate = date('Y-m-d H:i:s', strtotime("-0 days"));
        //$enddate = date('Y-m-d', strtotime("-10 days"));

        $enddate = date('Y-m-d H:i:s', strtotime("-20 days"));
        $coll= Mage::getModel('sales/order')->getCollection()
        									->addAttributeToFilter('created_at', array('from'=>$enddate, 'to'=>$startdate));
        
      	Mage::log("finishnowstart");

    
        $i=0;
        foreach($coll as $order)
		{
			$getstatusorder = $order->getStatus();
			$seestate = $order->getState();
			if ($seestate == "complete")
			{

				if($getstatusorder!= "really_confirmed" && $getstatusorder!= "return")
				{

				$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
           									->setOrderFilter($order)
        									->load();
        		foreach ($shipmentCollection as $shipment)
        		{
        		// This will give me the shipment IncrementId, but not the actual tracking information.
        			foreach($shipment->getAllTracks() as $tracknum)
            		{
                		
                		$carrier = $tracknum->getCarrierCode();
                		//mage::log($carrier);
                		//mage::log($tracknum);
                		//mage::log("^");
                		$tracknums[$i]=$tracknum->getNumber();
                		//Mage::log($tracknums[$i]);
                		//Mage::log("one loop over");

                		if($carrier == "pickrr")
                		{
                			$forstatus = $this->cronpickrr($tracknums[$i]);
                			$forstatusarray = explode("  ", $forstatus);
                			$finalstatus3 = $forstatusarray[1];
                			$finaldate = $forstatusarray[0];
                		}
                		elseif($carrier == "pyck")
                		{
                			$forstatus = $this->cronpyck($tracknums[$i]);
                			$forstatusarray = explode("  ", $forstatus);
                			$finalstatus3 = $forstatusarray[1];
                			$finaldate = $forstatusarray[0];
                		}	
                		elseif($carrier == "dlastmile")
                		{
                			$forstatus = $this->crondelhivery($tracknums[$i]);
                			$forstatusarray = explode("  ", $forstatus);
                			$finalstatus3 = $forstatusarray[1];
                			$finaldate = $forstatusarray[0];
                			mage::log("for delivery_status_date");
                			mage::log($forstatus);
                		}
                		else{
                			$finalstatus3 = "not found for delivery";
                		}	

	 				}
        						
				$i++;
			}

			//mage::log("forstatus is here:");
			//mage::log($forstatus);
			//mage::log($finalstatus3);
			//mage::log($finaldate);
			//mage::log($carrier);


				
					if($finalstatus3 == "In-Transit" || $finalstatus3 == "Pending" || $finalstatus3 == "Dispatched")
					{
						mage::log("this is the state: $seestate");
						$eid = $order->getEntityId();
						mage::log($eid);
			        	$order->setStatus("in_transit");  
			        	 $order->addStatusHistoryComment("$finaldate $finalstatus3 ");
			        	$order->save(); 	
					}

					if($finalstatus3 == "Returned" || $finalstatus3 == "RTO")
					{
						mage::log("this is the state: $seestate");
						$eid = $order->getEntityId();
						mage::log($eid);
			        	$order->setStatus("undelivered");  
			        	 $order->addStatusHistoryComment("$finaldate $finalstatus3 ");
			        	$order->save(); 
					}	

					if($finalstatus3 == "Delivered")
					{
						mage::log("this is the state: $seestate");
						$eid = $order->getEntityId();
						//mage::log($eid);
			        	$order->setStatus("delivered"); 
			        	 $order->addStatusHistoryComment("$finaldate $finalstatus3 ");
			        	 Mage::log("this delivered for this $eid"); 
			        	$order->save(); 
					}

					if($finalstatus3 == "Not Picked")
					{
						mage::log("this is the state: $seestate");
						$eid = $order->getEntityId();
						mage::log($eid);
						$order->setStatus("not_picked"); 
						$order->addStatusHistoryComment("$finaldate $finalstatus3 "); 
						$order->save(); 
					}
						if($finalstatus3 == "Manifested")
					{
						mage::log("this is the state: $seestate");
						$eid = $order->getEntityId();
						mage::log($eid);
						$order->setStatus("complete");
						$order->addStatusHistoryComment("$finaldate $finalstatus3 "); 
						$order->save(); 
					}

						if($finalstatus3 = "not found for delivery")
					{
						$order->addStatusHistoryComment("$finalstatus3 "); 
					}		


					

		    	} 

		    } 

	}
	
} //function ends here

	public function crondelhivery($value)
{
	$apiurl =  Mage::getStoreConfig('carriers/dlastmile/gateway_url');
      	$token =   Mage::getStoreConfig('carriers/dlastmile/licensekey');
	$url = $apiurl.'api/packages/json/?verbose=0&token='.$token.'&waybill='.$value;
			      	$options = array(
			        CURLOPT_RETURNTRANSFER => true,     // return web page
			        CURLOPT_HEADER         => false,    // don't return headers
			        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			        CURLOPT_ENCODING       => "",       // handle all encodings
			        CURLOPT_USERAGENT      => "spider", // who am i
			        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			        CURLOPT_TIMEOUT        => 120,      // timeout on response
			        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			        CURLOPT_SSL_VERIFYPEER => false,
				    );

				    $ch      = curl_init( $url );
				    curl_setopt_array( $ch, $options );
				    $content = curl_exec( $ch );
			    	$err     = curl_errno( $ch );
			   		$errmsg  = curl_error( $ch );
			    	$header  = curl_getinfo( $ch );
			    	curl_close( $ch );

			    	//$header['errno']   = $err;
			    	//$header['errmsg']  = $errmsg;
			    	//$header['content'] = $content;
			    	//Mage::log($header);
			    	mage::log($content);
				    $gettingstatus = json_decode($content, true);
				    $finalstatus1 = $gettingstatus['ShipmentData'][0];
				    $finalstatus2 = $finalstatus1['Shipment']['Status'];
				    $finalstatus3 = $finalstatus2['Status'];
				    $finalstatus3time = $finalstatus2['StatusDateTime'];
				    mage::log("final stats:");
				    mage::log($finalstatus3);
				    mage::log($finalstatus3time);
				    $trimmeddate = (explode("T",$finalstatus3time));
				    mage::log($trimmeddate[0]);
				    $scandate = $trimmeddate[0];


				    return ($scandate."  ".$finalstatus3);



}	


	public function cronpyck($value)
{
	$url = "http://www.pyck.in/api/packages/track/?username=stuti@rekinza.com&key=9b12ab55-1e10-4d79-8a5a-67ca44c09db8&waybill=$value";
			      	$options = array(
			        CURLOPT_RETURNTRANSFER => true,     // return web page
			        CURLOPT_HEADER         => false,    // don't return headers
			        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			        CURLOPT_ENCODING       => "",       // handle all encodings
			        CURLOPT_USERAGENT      => "spider", // who am i
			        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			        CURLOPT_TIMEOUT        => 120,      // timeout on response
			        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			        CURLOPT_SSL_VERIFYPEER => false,
				    );

				    $ch      = curl_init( $url );
				    curl_setopt_array( $ch, $options );
				    $contentold = curl_exec( $ch );
			    	$err     = curl_errno( $ch );
			   		$errmsg  = curl_error( $ch );
			    	$header  = curl_getinfo( $ch );
			    	curl_close( $ch );

			    	$header['errno']   = $err;
			    	$header['errmsg']  = $errmsg;
			    	$header['content'] = $contentold;

			    	$content = json_decode($contentold, true);
			    	//mage::log($content);
			    	mage::log("content got:");

			    	$beforescan = $content['results'][0];
			    	$scans = $beforescan['scans'];
			    	$scanslength = count($scans);
			    	$scanslength--;
			    	$scanstatus = $scans[0]['status'];
			    	$scandate = $scans[0]['date'];
			    	 mage::log("all printed");
			    	 mage::log($scanslength);
			    	  mage::log($scanstatus); 

			    	 if($scanstatus=="delivered"){
			    		return ($scandate."  Delivered");
			    	}
	    		 
			    	elseif($scanstatus=="in transit" || $scanstatus=="Received At hub"){
			    		return ($scandate."  In-Transit");
			    	}	
			    	elseif($scanstatus=="manifested"){
			    		return ($scandate."  Manifested");
			    	}
			    	elseif ($scanstatus=="dispatched") {
			    			return ($scandate."  Dispatched");
			    	}
			    	elseif ($scanstatus=="returned" || $scanstatus=="undelivered shipment held at location") {
			    			return ($scandate."  Returned");
			    	}  




}

public function cronpickrr($value)
{
	$url = "http://www.pickrr.com/api/tracking/$value/";
			      	$options = array(
			        CURLOPT_RETURNTRANSFER => true,     // return web page
			        CURLOPT_HEADER         => false,    // don't return headers
			        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			        CURLOPT_ENCODING       => "",       // handle all encodings
			        CURLOPT_USERAGENT      => "spider", // who am i
			        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
			        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
			        CURLOPT_TIMEOUT        => 120,      // timeout on response
			        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			        CURLOPT_SSL_VERIFYPEER => false,
				    );

				    $ch      = curl_init( $url );
				    curl_setopt_array( $ch, $options );
				    $contentold = curl_exec( $ch );
			    	$err     = curl_errno( $ch );
			   		$errmsg  = curl_error( $ch );
			    	$header  = curl_getinfo( $ch );
			    	curl_close( $ch );

			    	$header['errno']   = $err;
			    	$header['errmsg']  = $errmsg;
			    	$header['content'] = $contentold;

			    	$content = json_decode($contentold, true);

			    	$final_status = $content['final_status'];
			    	$final_status_date = $content['final_status_date'];
			    	//$delivery_status_date = $content['delivery_status_date'];
			    	$transit_status_date = $content['transit_status_date'];
			    	$warehouse_status_date = $content['warehouse_status_date'];
			    	$pickup_status_date = $content['pickup_status_date'];
			    	$order_status_date = $content['order_status_date'];
			    	$cancelled = $content['is_cancelled']; //make it returned and check after cancelled
			    	mage::log("printit");
			    	mage::log($delivery_status_date);
			    	mage::log($transit_status_date);
			    	mage::log($warehouse_status_date);
			    	mage::log($pickup_status_date);
			    	mage::log("all printed");

			    	if( !is_null($final_status)){
			    		if($final_status == "delivered"){
						return ($final_status_date."  Delivered");
						}
						if($final_status == "rto"){
						return ($final_status_date."  Returned");
						}
			    	}			    		
			    	elseif( !is_null($transit_status_date)){
			    		return ($transit_status_date."  In-Transit");
			    	}	
			    	elseif(!is_null($warehouse_status_date)){
			    		return ($warehouse_status_date."  Pending");
			    	}
			    	elseif (!is_null($pickup_status_date)) {
			    			return ($pickup_status_date."  Pending");
			    	}
			    	elseif (!is_null($order_status_date)) {
			    			return ($order_status_date."  Manifested");
			    	}	

}

		

} //class here



?>