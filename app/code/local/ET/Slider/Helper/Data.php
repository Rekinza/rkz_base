<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Helper_Data extends Mage_Core_Helper_Abstract {
	
	public $_slider_configs = null;
	
	private function _getConfig(){
		if ( !isset($this->_slider_configs) ){
			$this->_slider_configs = new Varien_Object();
			if ( $store_config = Mage::getStoreConfig( 'et_slider_configs' ) ){
				foreach ( $store_config as $group => $values ){
					foreach ( $values as $k => $v ){
						$this->_slider_configs->setData($k, $v);
					}
				}
			}
		}
		return $this->_slider_configs ? $this->_slider_configs : new Varien_Object();
	}
	
	public function getConfig( $name, $default = null ){
		return $this->_getConfig()->getDataSetDefault($name, $default);
	}
	
}
