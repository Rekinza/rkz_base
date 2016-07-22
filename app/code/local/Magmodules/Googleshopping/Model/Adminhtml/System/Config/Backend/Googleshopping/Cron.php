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
 
class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Backend_Googleshopping_Cron extends Mage_Core_Model_Config_Data {

    const CRON_MODEL_PATH = 'googleshopping/generate/cron_schedule';

    protected function _afterSave() {

        $enabled = $this->getData('groups/generate/fields/enabled/value');
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/fields/frequency/value');

        $frequencyDaily = Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency::CRON_DAILY;
        $frequencyWeekly = Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Frequency::CRON_MONTHLY;

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',       # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',        # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')->load(self::CRON_MODEL_PATH, 'path')->setValue($cronExprString)->setPath(self::CRON_MODEL_PATH)->save();
        } catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }

}
