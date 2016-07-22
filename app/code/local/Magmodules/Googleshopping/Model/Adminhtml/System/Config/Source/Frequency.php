<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Googleshopping
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */

class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency {

    protected static $_options;
    const CRON_DAILY    = 'D';
    const CRON_WEEKLY   = 'W';
    const CRON_MONTHLY  = 'M';

    public function toOptionArray() {
        if(!self::$_options) {
            self::$_options = array(
                array('label' => Mage::helper('adminhtml')->__('Daily'), 'value' => self::CRON_DAILY),
                array('label' => Mage::helper('adminhtml')->__('Weekly'), 'value' => self::CRON_WEEKLY),
                array('label' => Mage::helper('adminhtml')->__('Monthly'), 'value' => self::CRON_MONTHLY),
            );
        }
        return self::$_options;
    }

}