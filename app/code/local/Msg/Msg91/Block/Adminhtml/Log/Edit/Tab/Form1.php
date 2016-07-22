<?php

class Msg_Msg91_Block_Adminhtml_Log_Edit_Tab_Form1 extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        if (Mage::getSingleton('adminhtml/session')->getLogData()) {
            $data = Mage::getSingleton('adminhtml/session')->getLoglData();
            Mage::getSingleton('adminhtml/session')->getLogData(null);
        } elseif (Mage::registry('log_data')) {
            $data = Mage::registry('log_data')->getData();
        } else {
            $data = array();
        }

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('log_form', array(
            'legend' => Mage::helper('msg91')->__('Log Information')
                ));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('msg91')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('msg91')->__('Status'),
            'name' => 'status',
            'values' => array(
                  array(
                    'value' =>0,
                    'label' => Mage::helper('msg91')->__('Processing'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('msg91')->__('Sent'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('msg91')->__('Not Sent'),
                ),
                array(
                    'value' => 3,
                    'label' => Mage::helper('msg91')->__('Queued'),
                )
            ),
        ));
        $form->setValues($data);

        return parent::_prepareForm();
    }

}
