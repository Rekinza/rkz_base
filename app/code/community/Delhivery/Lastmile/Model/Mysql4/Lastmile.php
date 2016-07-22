<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Define Mysql resource for Waybills
 */
class Delhivery_Lastmile_Model_Mysql4_Lastmile extends Mage_Core_Model_Mysql4_Abstract
{
     /**
	 * construct mysql resource model for waybills table and set primary key
	 */	
    public function _construct()
    {    
        // Note that the lastmile_id refers to the key field in your database table.
        $this->_init('lastmile/lastmile', 'lastmile_id');
    }
}