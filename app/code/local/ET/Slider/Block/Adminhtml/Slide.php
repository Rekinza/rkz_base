<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Slide extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_slide';
		$this->_blockGroup = 'slider';
		$this->_headerText = Mage::helper('slider')->__('Manage Slides');
		$this->_addButtonLabel = Mage::helper('slider')->__('New Slide');
		parent::__construct();
	}
}