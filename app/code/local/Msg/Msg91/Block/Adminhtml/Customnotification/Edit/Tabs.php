<?php

class Msg_Msg91_Block_Adminhtml_Customnotification_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customnotification_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('msg91')->__('Custom Notifications'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('msg91')->__('Custom Notification Information'),
          'title'     => Mage::helper('msg91')->__('Custom Notification Information'),
          'content'   => $this->getLayout()->createBlock('msg91/adminhtml_customnotification_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
