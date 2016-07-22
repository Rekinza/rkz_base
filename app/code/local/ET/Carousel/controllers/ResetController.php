<?php
//HEADER_COMMENT//

class ET_Carousel_ResetController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Reset Module
	 * Remove setup infomation from core_resource
	 * Remove configuration values from core_config_data
	 *
	 * Usage: /index.php/carousel/reset
	 */
	
	public function indexAction() {
		$db = Mage::getSingleton('core/resource')->getConnection('core_write');
		$db->query("DELETE FROM core_resource WHERE code = 'carousel_setup';");
		$db->query("DELETE FROM core_config_data WHERE path LIKE '%carousel_configs/%';");
		ob_start();
		echo '<b>Default Config Section: et_carousel_configs</b>';
		echo '<pre>';
		
		print_r( Mage::getStoreConfig('et_carousel_configs') );
		
		echo '<a href="'.$this->_getRefererUrl().'">Go back</a>';
		echo '<a href="'.Mage::getUrl('*/index').'" id="redir">Index</a>';
		echo "
			<script>
				setTimeout(function(){ window.location.href=document.getElementById('redir').href; }, 5000);
			</script>
		";
		
		$content = ob_get_clean();
		$this->getResponse()->setBody($content);
	}
}