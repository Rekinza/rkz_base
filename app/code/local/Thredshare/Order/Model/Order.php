<?php 
class Thredshare_Order_Model_Order extends Mage_Sales_Model_Order{


	protected function _setState($state, $status = false, $comment = '',
        $isCustomerNotified = null, $shouldProtectState = false)
    {
		parent::_setState($state,$status,$comment,$isCustomerNotified,$shouldProtectState);
		
		Mage::dispatchEvent('sales_order_status_after', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified, 'shouldProtectState' => $shouldProtectState));
		return $this;
	}
	
	 public function addStatusHistoryComment($comment, $status = false)
    {
		
		$history=parent::addStatusHistoryComment($comment, $status);
		Mage::dispatchEvent('sales_order_status_after', array('order' => $this, 'state' => "", 'status' => $status));
		Mage::dispatchEvent('forstylesister', array('order' => $this, 'state' => $state, 'status' => $status, 'comment' => $comment, 'isCustomerNotified' => $isCustomerNotified, 'shouldProtectState' => $shouldProtectState));

		return $history;
    }

}

?>