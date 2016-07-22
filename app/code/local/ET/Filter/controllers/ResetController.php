<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_ResetController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Reset Module
	 * Remove setup infomation from core_resource
	 * Remove configuration values from core_config_data
	 *
	 * Usage: /index.php/filter/reset
	 */
	
	public function indexAction() {
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query("DELETE FROM core_resource WHERE code = 'filter_setup';");
		$db->query("DELETE FROM core_config_data WHERE path LIKE 'et_filter_configs/%';");
		ob_start();
		echo '<b>Default Config Section: et_filter_configs</b>';
		echo '<pre>';
		print_r( Mage::getStoreConfig('et_filter_configs') );
		echo '<a href="'.$this->_getRefererUrl().'">Go back</a>';
		echo '<a href="'.Mage::getUrl('*/index').'" id="redir">Index</a>';
		echo "
			<script>
				setTimeout(function(){ window.location.href=document.getElementById('redir').href; }, 3000);
			</script>
		";
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
}