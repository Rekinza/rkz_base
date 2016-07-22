<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Caption extends Mage_Core_Block_Template {
	public function _prepareLayout() {
		return parent::_prepareLayout();
	}

	public function getCaption() {
		if(!$this->hasData('caption')) {
			$this->setData('caption', Mage::registry('caption'));
		}
		return $this->getData('caption');
	}
}