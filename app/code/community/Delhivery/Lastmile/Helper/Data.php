<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Helper file for common functions
 */
class Delhivery_Lastmile_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function getLastmileUrl(){
        return $this->_getUrl('lastmile');
    }
	/*
	* Function to execute curl
	* @return API response
	*/
    public function Executecurl($url, $type, $params){
        	$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "$url");
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			if($type == 'post'):
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			endif;	
			$retValue = curl_exec($ch);
			//mage::log($retValue);
			curl_close($ch);
			return $retValue;	
    }
}
