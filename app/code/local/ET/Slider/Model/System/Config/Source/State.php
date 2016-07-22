<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_State extends Varien_Object {
    const STATE_UNPUBLIESHED = 2;
    const STATE_PUBLISHED = 1;

    public static function getOptionArray(){
        $opts = array(
            self::STATE_PUBLISHED    => Mage::helper('slider')->__('Published'),
            self::STATE_UNPUBLIESHED => Mage::helper('slider')->__('Unpublished')
        );
        return $opts;
    }
    
    public static function toOptionArray(){
        return array(
			array(
			  'value'     => self::STATE_PUBLISHED,
			  'label'     => Mage::helper('slider')->__('Published'),
			),
			array(
			  'value'     => self::STATE_UNPUBLIESHED,
			  'label'     => Mage::helper('slider')->__('Unpublished'),
			),
		);
    }
}