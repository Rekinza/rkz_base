<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_System_Config_Source_MenuType extends Varien_Object {
	
	const SEPARATOR = 1; // use item as text separator // nothing
	const EXTERNAL  = 2; // link to an external link // text
	const ROUTER    = 3; // link to magento route // select
	
	const PRODUCT   = 4; // catalog product link // choiser
	const CATEGORY  = 5; // catalog category link // choiser
	const PAGE      = 6; // cms page link // choiser
	
	const CONTENT   = 7; // use description as dropdown content instead of children
	const DIVIDER   = 8; // use item as menu divider
	
	public static function getOptionArray() {
		return array (
				// self::SEPARATOR => Mage::helper('megamenu')->__( 'Text Separator' ),
				self::EXTERNAL  => Mage::helper('megamenu')->__( 'External Link' ),
				// self::ROUTER    => Mage::helper('megamenu')->__( 'Frontend Page' ),
				self::PRODUCT   => Mage::helper('megamenu')->__( 'Catalog Product Link' ),
				self::CATEGORY  => Mage::helper('megamenu')->__( 'Catalog Category Link' ),
				self::PAGE      => Mage::helper('megamenu')->__( 'CMS Page Link' ),
				self::CONTENT   => Mage::helper('megamenu')->__( 'Menu Content' ),
				self::DIVIDER   => Mage::helper('megamenu')->__( 'Menu Divider' )
		);
	}
	public static function toOptionArray() {
		return array (
// 				array (
// 						'value' => self::SEPARATOR,
// 						'label' => Mage::helper('megamenu')->__( 'Text Separator' )
// 				),
				
				array (
						'value' => self::EXTERNAL,
						'label' => Mage::helper('megamenu')->__( 'External Link' )
				),
				
// 				array (
// 						'value' => self::ROUTER,
// 						'label' => Mage::helper('megamenu')->__( 'Frontend Page' )
// 				),
				
				array (
						'value' => self::PRODUCT,
						'label' => Mage::helper('megamenu')->__( 'Catalog Product Link' )
				),
				array (
						'value' => self::CATEGORY,
						'label' => Mage::helper('megamenu')->__( 'Catalog Category Link' )
				),
				array (
						'value' => self::PAGE,
						'label' => Mage::helper('megamenu')->__( 'CMS Page Link' )
				),
				array (
						'value' => self::CONTENT,
						'label' => Mage::helper('megamenu')->__( 'Menu Content' )
				),
				array (
						'value' => self::DIVIDER,
						'label' => Mage::helper('megamenu')->__( 'Menu Divider' )
				)
		);
	}
}