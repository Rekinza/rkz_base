<?php
class Msg_Msg91_Block_Adminhtml_Notificationlog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'msg91';
        $this->_controller = 'adminhtml_notificationlog';
  $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('msg91')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('msg91')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('notificationlog_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'notificationlog_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'notificationlog_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('notificationlog_data') && Mage::registry('notificationlog_data')->getId()) {
            return Mage::helper('msg91')->__('Edit Notificationlog ');
        } else {
            return Mage::helper('msg91')->__('New Notificationlog');
        }
    }

}
