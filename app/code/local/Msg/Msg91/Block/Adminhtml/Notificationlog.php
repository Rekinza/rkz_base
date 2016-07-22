<?php
 
class Msg_Msg91_Block_Adminhtml_Notificationlog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_addButtonLabel = 'Add New Log';
 
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_notificationlog';
        $this->_blockGroup = 'msg91';
        $this->_headerText = Mage::helper('msg91')->__('Notification Logs');
        $this->removeButton('add'); 
    }
}