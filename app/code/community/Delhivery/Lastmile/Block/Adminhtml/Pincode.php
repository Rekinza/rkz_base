<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2010-2011 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose of the the file - Main Block file for Pincode Section
 */
class Delhivery_Lastmile_Block_Adminhtml_Pincode extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_pincode';
        $this->_blockGroup = 'lastmile';
        $this->_headerText = Mage::helper('lastmile')->__('Pincode Manager');
        $this->_addButtonLabel = Mage::helper('lastmile')->__('Download Pincode');
		$this->_addButton('button1', array(
			   'label'      => Mage::helper('lastmile')->__('Download Pincode'),
			   'onclick'    => 'setLocation(\'' . $this->getUrl('*/*/fetch') . '\')',
			   'class'      => 'add' 
			));	        
		parent::__construct();
		$this->_removeButton('add');
		
    }

}