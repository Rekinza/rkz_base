<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Model_System_Config_Source_Slider_Submit_Type
{

    const SUBMIT_AUTO_DELAYED = 1;
    const SUBMIT_BUTTON = 2;
    protected $_options;

    public function toOptionArray(){
        if (null === $this->_options) {
            $helper = Mage::helper('filter');
            $this->_options = array(
                self::SUBMIT_AUTO_DELAYED => $helper->__('Delayed auto submit'),
                self::SUBMIT_BUTTON => $helper->__('Submit button')
            );
        }

        return $this->_options;
    }

}