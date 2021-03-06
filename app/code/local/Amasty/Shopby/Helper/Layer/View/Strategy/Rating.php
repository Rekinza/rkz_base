<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Helper_Layer_View_Strategy_Rating extends Amasty_Shopby_Helper_Layer_View_Strategy_Abstract
{
    protected function setTemplate()
    {
        return 'amasty/amshopby/attribute.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getPosition();
    }

    protected function setHasSelection()
    {
        return isset($_GET['rating']);
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('amshopby/general/rating_collapsed');
    }
}
