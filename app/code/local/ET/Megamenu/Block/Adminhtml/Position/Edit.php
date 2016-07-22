<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Position_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Initialize megamenu_position edit block
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'megamenu';
		$this->_controller = 'adminhtml_position';
		
		$this->_updateButton( 'save', 'label', Mage::helper( 'cms' )->__( 'Save Position' ) );
		$this->_updateButton( 'delete', 'label', Mage::helper( 'cms' )->__( 'Delete Position' ) );
		
		$this->_addButton( 'saveandcontinue', array (
				'label' => Mage::helper( 'adminhtml' )->__( 'Save and Continue Edit' ),
				'onclick' => 'saveAndContinueEdit()',
				'class' => 'save'
		), -100 );
		
		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
	}
	
	/**
	 * Get edit form container header text
	 *
	 * @return string
	 */
	public function getHeaderText() {
		if (Mage::registry( 'position_data' ) && Mage::registry( 'position_data' )->getId()) {
			return Mage::helper('megamenu')->__( "Edit Position '%s'", $this->htmlEscape( Mage::registry( 'position_data' )->getTitle() ) );
		} else {
			return Mage::helper('megamenu')->__( 'New Position' );
		}
	}
	
	/**
	 * Prepare layout
	 *
	 * @return Mage_Core_Block_Abstract
	 */
	protected function _prepareLayout() {
		// Load Wysiwyg on demand and Prepare layout
		if (Mage::getSingleton( 'cms/wysiwyg_config' )->isEnabled () && ($block = $this->getLayout()->getBlock( 'head' ))) {
			$block->setCanLoadTinyMce( true );
		}
		parent::_prepareLayout ();
	}
}