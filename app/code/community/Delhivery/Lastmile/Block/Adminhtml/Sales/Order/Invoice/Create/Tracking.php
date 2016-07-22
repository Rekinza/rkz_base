<?php
/**
 * Delhivery
 * @category   Delhivery
 * @package    Delhivery_Godam
 * @copyright  Copyright (c) 2014 Delhivery. (http://www.delhivery.com)
 * @license    Creative Commons Licence (CCL)
 * @purpose    Override Default template for invoice tracking creation   
 */
class Delhivery_Lastmile_Block_Adminhtml_Sales_Order_Invoice_Create_Tracking extends Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Tracking
{
    public function _construct()
    {
        $this->setTemplate('delhiverylastmile/invoice/tracking.phtml');
    }
}
