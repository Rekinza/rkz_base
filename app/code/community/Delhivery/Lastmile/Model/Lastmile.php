<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Lastmile
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Model Class for Waybills  
 */
class Delhivery_Lastmile_Model_Lastmile extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('lastmile/lastmile');
    }

	 /**
	 * Function to get waybill details if waybill number is supplied
	 */	
	public function loadByAwb($awb)
    {
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT * FROM " . $resource->getTableName('lastmile/lastmile')." WHERE awb = $awb";
		mage::log("$query");		
		$data = $readConnection->fetchOne($query);
        return $data;
    }
	 /**
	 * Function to count unsed waybills for the current client
	 */	
	public function countUnsed()
    {
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT count(*) FROM " . $resource->getTableName('lastmile/lastmile')." WHERE state = 2";
		mage::log("$query");
		$data = $readConnection->fetchOne($query);
        return $data;
    }		
	 /**
	 * Function to get waybills that require status update
	 */	
    public function findAwbToUpdate()
    {		
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT awb FROM " . $resource->getTableName('lastmile/lastmile')." WHERE status IN ('InTransit','Dispatched','Pending')";
		mage::log("$query");
		$data = $readConnection->fetchAll($query);
        return $data;		
    }
	 /**
	 * Function to get waybills to be canceled if order is cancelled
	 */	
    public function findAwbToCancel($OrderId)
    {		
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT lastmile_id FROM " . $resource->getTableName('lastmile/lastmile')." WHERE orderid = $OrderId AND status = 'Assigned'";
		mage::log("$query");
		$data = $readConnection->fetchAll($query);
        return $data;		
    }	
}