<?php
 
class Msg_Msg91_Block_Adminhtml_Testmessage extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_addButtonLabel = 'Add New Log';
 
    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_testmessage';
        $this->_blockGroup = 'msg91';
        $this->_headerText = Mage::helper('msg91')->__('Testmessage');
        $this->removeButton('add'); 
    }
}
