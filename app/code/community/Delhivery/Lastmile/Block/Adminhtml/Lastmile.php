<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2010-2011 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Mail Block file for waybills section 
 */
class Delhivery_Lastmile_Block_Adminhtml_Lastmile extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_controller = 'adminhtml_lastmile';
        $this->_blockGroup = 'lastmile';
        $this->_headerText = Mage::helper('lastmile')->__('AWB Manager');        
		$this->_addButton('button1', array(
			   'label'      => Mage::helper('lastmile')->__('Download AWB'),
			   'onclick'    => 'setLocation(\'' . $this->getUrl('*/*/fetch') . '\')',
			   'class'      => 'add' 
			));
		$this->_addButton('syncstatus', array(
			   'label'      => Mage::helper('lastmile')->__('Update AWB Status'),
			   'onclick'    => 'setLocation(\'' . $this->getUrl('*/*/syncstatus') . '\')',
			   'class'      => '' 
			));				   		
        parent::__construct();
		$this->_removeButton('add');
    }
}
     