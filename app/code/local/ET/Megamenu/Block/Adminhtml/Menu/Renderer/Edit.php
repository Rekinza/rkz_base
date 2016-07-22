<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Menu_Renderer_Edit extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		if ( $row->getParentId() == 0 ){
			return '<b>'.$row->getTitle().'</b>';
		}
		return "<a style='text-decoration: none;' href='".$this->getUrl('*/*/edit', array('id' => $row->getId()))."' >".$row->getTitle()."</a>";
	}
}