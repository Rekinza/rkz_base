<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Status options for grid view
 */
class Delhivery_Lastmile_Model_Status extends Varien_Object {
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    /**
     * Function to set global status object
     *
	 */		
    static public function getOptionArray() {
        return array(
            self::STATUS_ENABLED => Mage::helper('lastmile')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('lastmile')->__('Disabled')
        );
    }
}