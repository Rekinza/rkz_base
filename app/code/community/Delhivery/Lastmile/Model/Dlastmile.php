<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Model class for shipment plugin and tracking 
 */

class Delhivery_Lastmile_Model_Dlastmile extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'dlastmile';


    /**
     * Function to collect shipping charges for the plugin
     *
     * @return rate result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $handling = Mage::getStoreConfig('carriers/' . $this->_code . '/handling_fee');
        $result = Mage::getModel('shipping/rate_result');
        $show = true;
        if ($show) { // This if condition is just to demonstrate how to return success and error in shipping methods
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setMethod($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethodTitle($this->getConfigData('name'));
            $method->setPrice($this->getConfigData('handling_fee'));
            $method->setCost($this->getConfigData('handling_fee'));
            $result->append($method);
        } else {
            $error = Mage::getModel('shipping/rate_result_error');
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('name'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
        }
        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        //we only have one method so just return the name from the admin panel
        return array('aramex' => $this->getConfigData('title'));
    }
    /**
     * Set if tracking is available with the shipping method
     *
     * @return true/false
     */
    public function isTrackingAvailable() {      
		return true;
    }
    /**
     * Get tracking information of a order
     *
     * @return all tracking number
     */
    public function getTrackingInfo($tracking_number) {
        $tracking_result = $this->getTracking($tracking_number);

        if ($tracking_result instanceof Mage_Shipping_Model_Tracking_Result) {
            if ($trackings = $tracking_result->getAllTrackings()) {
                return $trackings[0];
            }
        } elseif (is_string($tracking_result) && !empty($tracking_result)) {
            return $tracking_result;
        }

        return false;
    }
    /**
     * Get current status of a waybill number
     *
     * @return tracking status data
     */
    public function getTracking($tracking_number) {
        $tracking_result = Mage::getModel('shipping/tracking_result');

        $tracking_status = Mage::getModel('shipping/tracking_result_status');
        $tracking_status->setCarrier($this->_code);
        $tracking_status->setCarrierTitle($this->getConfigData('carrier_title'));
        $tracking_status->setTracking($tracking_number);
        //Getting xml of shippment bu curl
        $path = $this->getConfigData('gateway_url') . 'api/packages/json/?verbose=0&token='.$this->getConfigData('licensekey').'&waybill=' . $tracking_number;
		mage::log($path);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $retValue = curl_exec($ch);
        curl_close($ch);
		$statusupdates = json_decode($retValue);
        try {
            $status = "";
			foreach ($statusupdates->ShipmentData as $item) {
					$status .= $item->Shipment->Status->Status . "<br/>";
			}
        } catch (Exception $e) {
            Mage::logException($e);
            $status = "Something went wrong while getting tracking information";
        }
        $tracking_status->addData(
                array(
                    'status' => $status
                )
        );
        $tracking_result->append($tracking_status);

        return $tracking_result;
    }

}
?>
