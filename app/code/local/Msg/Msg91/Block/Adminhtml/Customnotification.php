<?php
    
class Msg_Msg91_Block_Adminhtml_Customnotification extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_customnotification';
        $this->_blockGroup = 'msg91';
        $this->_headerText = Mage::helper('msg91')->__('Custom Notification Management');
        $this->_addButtonLabel = Mage::helper('msg91')->__('Add New');

        parent::__construct(); 
	//$this->_removeButton('add');
        //$this->_updateButton('add', 'label', Mage::helper('zone')->__('Add Options'));
    }
    
}
