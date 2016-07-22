<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Css_Position {
	public static $_positions = array(
		array( 'value'=>'absolute', 'label'=>'Absolute' ),
		array( 'value'=>'relative', 'label'=>'Relative' ),
		array( 'value'=>'static', 'label'=>'Static' ),
		array( 'value'=>'fixed', 'label'=>'Fixed' ),
		array( 'value'=>'inherit', 'label'=>'Inherit' )
	);
	
	public static function getOptionArray() {
		$options = array ();
		foreach ( self::$_positions as $pos ) {
			$options[$pos['value']] = $pos['label'];
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options[] = array (
			'value' => '',
			'label' => Mage::helper('slider')->__ ('--Please Select--')
		);
		foreach ( self::$_positions as $pos ) {
			$options[] = $pos;
		}
		return $options;
	}
}

