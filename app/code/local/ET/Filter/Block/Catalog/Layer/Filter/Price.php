<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price {

    public function __construct(){
        parent::__construct();
        if ($this->helper('filter')->isEnabled() && $this->helper('filter')->isPriceSliderEnabled()) {
			$this->setTemplate('et/filter/catalog/layer/price.phtml');
        }
    }

    public function getMaxPriceFloat(){
        return $this->_filter->getMaxPriceFloat();
    }

    public function getMinPriceFloat(){
        return $this->_filter->getMinPriceFloat();
    }

    public function getCurrentMinPriceFilter(){
        list($from, $to) = $this->_filter->getInterval();
        $from = floor((float) $from);

        if ($from < $this->getMinPriceFloat()) {
            return $this->getMinPriceFloat();
        }
        return $from;
    }

    public function getCurrentMaxPriceFilter() {
        list($from, $to) = $this->_filter->getInterval();
        $to = floor((float) $to);

        if ($to == 0 || $to > $this->getMaxPriceFloat()) {
            return $this->getMaxPriceFloat();
        }
		
        return $to;
    }

    public function getUrlPattern(){
        $item = Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this->_filter)
            ->setValue('__PRICE_VALUE__')
            ->setCount(0);

        return $item->getUrl();
    }

    public function isSubmitTypeButton(){
        $type = $this->helper('filter')->getPriceSliderSubmitType();

        if ($type == ET_Filter_Model_System_Config_Source_Slider_Submit_Type::SUBMIT_BUTTON) {
            return true;
        }

        return false;
    }

    public function getItemsCount(){
        if ($this->helper('filter')->isEnabled() && $this->helper('filter')->isPriceSliderEnabled()) {
            return 1;
        }

        return parent::getItemsCount();
    }

}
