<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Caption extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_caption';
		$this->_blockGroup = 'slider';
		$this->_headerText = Mage::helper('slider')->__('Manage Captions');
		$this->_addButtonLabel = Mage::helper('slider')->__('New Caption');
		parent::__construct();
	}
}