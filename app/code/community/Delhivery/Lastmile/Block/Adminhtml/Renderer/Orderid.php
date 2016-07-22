<?php
/**
 * Axiswebart
 * @category   Axisweb
 * @package    Axisweb_Trade
 * @copyright  Copyright (c) 2010-2011 Axiswebart. (http://www.axiswebart.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Render current order status for a waybill
 */
class Delhivery_Lastmile_Block_Adminhtml_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

     public function render(Varien_Object $row)
    {
		if($row->getOrderid() != NULL)
		{
        $order = Mage::getModel('sales/order')->load($row->getOrderid());
		return $order->getIncrementId();
		}
		else
		return "";
    }
	 

}