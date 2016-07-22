<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Css_Overflow {
	public static $_overflows = array(
		array( 'value'=>'auto', 'label'=>'Auto' ),
		array( 'value'=>'hidden', 'label'=>'Hidden' ),
		array( 'value'=>'scroll', 'label'=>'Scroll' ),
		array( 'value'=>'visible', 'label'=>'Visible' ),
		array( 'value'=>'inherit', 'label'=>'Inherit' )
	);
	
	public static function getOptionArray() {
		$options = array ();
		foreach ( self::$_overflows as $el ) {
			$options[$el['value']] = $el['label'];
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options[] = array (
			'value' => '',
			'label' => Mage::helper('slider')->__ ('--Please Select--')
		);
		foreach ( self::$_overflows as $el ) {
			$options[] = $el;
		}
		return $options;
	}
}

