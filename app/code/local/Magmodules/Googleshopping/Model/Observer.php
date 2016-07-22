<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Googleshopping
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Googleshopping_Model_Observer {

    public function scheduledGenerateGoogleshopping($schedule) {

    	$enabled = Mage::getStoreConfig('googleshopping/general/enabled');
    	$cron = Mage::getStoreConfig('googleshopping/generate/cron');
    	
		if($enabled && $cron) {

			$html = '';		
			$results = array();
			$storeIds = Mage::helper('googleshopping')->getStoreIds(); 		
			
			foreach($storeIds as $storeId) {
				if(Mage::getStoreConfig('googleshopping/generate/enabled', $storeId)) {
					$results[] = Mage::getModel("googleshopping/googleshopping")->generateFeed($storeId);						
				}	
			}							

			foreach($results as $result) {
				$html .= '<tr><td>' . $result['shop'] . '</td><td><a href="' . $result['url'] . '">' . $result['url'] . '</a><br/>Date: ' . $result['date'] . ' - Products: ' . $result['qty'] . '</td></tr>';
			}

			if($html) {
				$html_header = '<div class="grid"><table cellpadding="0" cellspacing="0" class="border"><tbody><tr class="headings"><th>store</th><th>URL</th></tr>';
				$html_footer = '</tbody></table></div>';
				$html = $html_header . $html . $html_footer;			
				$config = new Mage_Core_Model_Config();
				$config->saveConfig('googleshopping/generate/feeds', $html, 'default', 0);
				Mage::app()->getCacheInstance()->cleanType('config');
			}

		}   	      

    }
    
}
