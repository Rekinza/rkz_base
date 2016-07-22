<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Widget_Chooser extends Mage_Widget_Block_Adminhtml_Widget_Chooser {
	public function getSourceUrl() {
		if(! $this->_getData('admin_source_url')) {
			$admin_frontname =( string ) Mage::getConfig()->getNode('admin/routers/adminhtml/args/frontName');
			$source_url = parent::getSourceUrl();
			$admin_source_url = str_replace('/slider/', '/' . $admin_frontname . '/', $source_url );
			$this->setData('admin_source_url', $admin_source_url );
		}
		return $this->_getData('admin_source_url');
	}
}
