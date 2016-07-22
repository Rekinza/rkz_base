<?php

class Msg_Msg91_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('msg91')->__('Sent'),
            self::STATUS_DISABLED   => Mage::helper('msg91')->__('Not Sent')
        );
    }
}