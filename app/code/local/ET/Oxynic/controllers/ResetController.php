<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_ResetController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Reset Module
	 * Remove setup infomation from core_resource
	 * Remove configuration values from core_config_data
	 *
	 * Usage: /index.php/oxynic/reset
	 */
	
	public function indexAction() {
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query("DELETE FROM core_resource WHERE code = 'oxynic_setup';");
		$db->query("DELETE FROM core_config_data WHERE path LIKE '%oxynic_configs/%';");
		ob_start();
		echo '<b>Default Config Section: et_oxynic_configs</b>';
		echo '<pre>';
		$helper = Mage::helper('oxynic');
		
		print_r( $helper->getAllConfig() );
		
		echo '<a href="'.$this->_getRefererUrl().'">Go back</a>';
		
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
}