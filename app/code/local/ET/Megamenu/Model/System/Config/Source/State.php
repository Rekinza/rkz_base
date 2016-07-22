<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_State extends Varien_Object {
	const STATE_UNPUBLIESHED = 0;
    const STATE_PUBLISHED = 1;

    public static function getOptionArray(){
        $opts = array(
            self::STATE_PUBLISHED    => Mage::helper('megamenu')->__('Published'),
            self::STATE_UNPUBLIESHED => Mage::helper('megamenu')->__('Unpublished')
        );
        return $opts;
    }
    
    public static function toOptionArray(){
        return array(
			array(
			  'value'     => self::STATE_PUBLISHED,
			  'label'     => Mage::helper('megamenu')->__('Published'),
			),
			array(
			  'value'     => self::STATE_UNPUBLIESHED,
			  'label'     => Mage::helper('megamenu')->__('Unpublished'),
			),
		);
    }
}