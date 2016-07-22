<?php

class Loginradius_Sociallogin_Model_Backend_Validator extends Mage_Core_Model_Config_Data
{
  protected function _beforeSave()
{
  echo "hello";die;
  $value = $this->getValue();
  if (!Zend_Validate::is($value, 'EmailAddress')) {
    Mage::throwException(Mage::helper('adminhtml')->__('Invalid email address "%s".', $value));
  }
  return $this;
}
  protected function _afterLoad(){
    echo "hello";die;
  }
}