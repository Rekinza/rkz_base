<?php

class Msg_Msg91_Block_Adminhtml_Notificationlog_Edit_Tab_Form1 extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        if (Mage::getSingleton('adminhtml/session')->getNotificationLogData()) {
            $data = Mage::getSingleton('adminhtml/session')->getNotificationLoglData();
            Mage::getSingleton('adminhtml/session')->getNotificationLogData(null);
        } elseif (Mage::registry('notificationlog_data')) {
            $data = Mage::registry('notificationlog_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('notificationlog_form', array(
            'legend' => Mage::helper('msg91')->__('Notification Log Information')
                ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('msg91')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('msg91')->__('Sent To MSG91'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('msg91')->__('Not Sent'),
                ),
            ),
        ));

        $form->setValues($data);

        return parent::_prepareForm();
    }

}
