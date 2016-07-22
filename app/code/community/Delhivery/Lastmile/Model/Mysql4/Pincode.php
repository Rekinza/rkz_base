<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Define Mysql resource for pincode
 */
class Delhivery_Lastmile_Model_Mysql4_Pincode extends Mage_Core_Model_Mysql4_Abstract {

     /**
	 * construct mysql resource model for pincode table and set primary key
	 */	
	public function _construct() {
        $this->_init('lastmile/pincode', 'pincode_id');
    }

}