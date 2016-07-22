<?php

class Msg_Msg91_Block_Adminhtml_Customnotification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
       
        $this->_objectId = 'rule_id';
        $this->_blockGroup = 'msg91';
        $this->_controller = 'adminhtml_customnotification';
$this->_mode = 'edit';  

 parent::__construct();
        
        //  $this->_updateButton('save', 'label', Mage::helper('msg91')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('msg91')->__('Delete Item'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'edit_form');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'edit_form');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('rule_data') && Mage::registry('rule_data')->getId()) {
            return Mage::helper('msg91')->__('Edit Rule "%s"', $this->htmlEscape(Mage::registry('rule_data')->getName()));
        } else {
            return Mage::helper('msg91')->__('New Rule');
        }
    }

}
