<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_controller = 'adminhtml_menu';
		$this->_blockGroup = 'megamenu';
		$this->_headerText = Mage::helper('megamenu')->__('Manage Menu');
		$this->_addButtonLabel = Mage::helper('megamenu')->__('New Menu');
		parent::__construct ();
	}
}