<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Observer Class to catch magento event to perform required task  
 */
    class Delhivery_Lastmile_Model_Observer
    {				
		/**
		* Function to update waybill if order tracking is of Delhivery Lastmile
		*/
        public function sales_shipment_add_tracking($observer)
        {
			$track = $observer->getEvent()->getTrack();
			$order = $track->getShipment()->getOrder();
			$shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
			if (!$shippingMethod) {
				return;
			}
			// Process only Delhivery Lastmile methods
			if($track->getCarrierCode() != 'dlastmile')
			{
				return;
			}
			//mage::log($track->getNumber());
			//mage::log($track->getCarrierCode());
			$model = Mage::getModel('lastmile/lastmile');
			$awbobj = $model->loadByAwb($track->getNumber());
			$data = array();
			$status = 'Assigned';
			$data['state'] = 1;
			$data['status'] = "Assigned";
			$data['orderid'] = $order->getId();
			$data['shipment_to'] = $order->getShippingAddress()->getName();
			$data['shipment_id'] = $track->getShipment()->getIncrementId();
			mage::log($data);
			$model->setData($data);
			$model->setId($awbobj);			
			$model->save();
			return;	
        }
		
		 /**
		 * Function to remove AWB from an order if Tracking is deleted
		 */
		public function sales_shipment_remove_tracking($observer)
        {		
			$track = $observer->getEvent()->getTrack();
			$order = $track->getShipment()->getOrder();
			$shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
			if (!$shippingMethod) {
				return;
			}
			// Process only Delhivery Lastmile methods
			if($track->getCarrierCode() != 'dlastmile')
			{
				return;
			}
	
			$model = Mage::getModel('lastmile/lastmile');
			$awbobj = $model->loadByAwb($track->getNumber());
			$data = array();
			$data['state'] = 2;
			$data['status'] = NULL;
			$data['orderid'] = NULL;
			$model->setData($data);
			$model->setId($awbobj);			
			$model->save();
			return;	
		}
		/**
		* Function to remove AWB from an order if Order is Canceled
		*/		
		public function sales_order_cancel_remove_tracking($observer)
        {		
			$orderid = $observer->getEvent()->getOrder()->getId();
			$AwbsToCancel = Mage::getModel('lastmile/lastmile')->findAwbToCancel($orderid);			
			if(count($AwbsToCancel))
			{				
				$model = Mage::getModel('lastmile/lastmile');
				$data = array();
				$data['state'] = 2;
				$data['status'] = NULL;
				$data['orderid'] = NULL;
				foreach($AwbsToCancel as $Awb)
				{
					$model->setData($data);
					$model->setId($Awb['lastmile_id']);			
					$model->save();				
				}
			}

			return;	
		}		
}