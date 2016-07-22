<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Position extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
		$this->_addButton( 'import_from_catalog', array (
				'label' => Mage::helper('megamenu')->__( 'Synchronize w/ Magento Catalog' ),
				'onclick' => "location.href='" . $this->getUrl ( '*/*/importFromCatalog' ) . "'",
				'class' => ''
		));
		
		$this->_controller = 'adminhtml_position';
		$this->_blockGroup = 'megamenu';
		$this->_headerText = Mage::helper('megamenu')->__( 'Manage Position' );
		$this->_addButtonLabel = Mage::helper('megamenu')->__( 'Add New Position' );
		parent::__construct ();
	}
}