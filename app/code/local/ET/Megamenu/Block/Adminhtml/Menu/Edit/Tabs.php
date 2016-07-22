<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Menu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'menu_tabs' );
		$this->setDestElementId ( 'edit_form' );
		$this->setTitle ( Mage::helper('megamenu')->__( 'Menu Infomation' ) );
	}
	
	protected function _beforeToHtml() {
		$this->addTab ( 'form_section', array (
				'label' => Mage::helper('megamenu')->__( 'Properties' ),
				'title' => Mage::helper('megamenu')->__( 'Properties' ),
				'content' => $this->getLayout ()->createBlock ( 'megamenu/adminhtml_menu_edit_tab_properties' )->toHtml ()
		) );
		
		return parent::_beforeToHtml ();
	}
}