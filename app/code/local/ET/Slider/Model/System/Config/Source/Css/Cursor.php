<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Css_Cursor {
	public static $_cursors = array(
		array( 'value'=>'default', 'label'=>'Default' ),
		array( 'value'=>'auto', 'label'=>'Auto' ),
		array( 'value'=>'crosshair', 'label'=>'Crosshair' ),
		array( 'value'=>'pointer', 'label'=>'Pointer' ),
		array( 'value'=>'move', 'label'=>'Move' ),
		array( 'value'=>'text', 'label'=>'Text' ),
		array( 'value'=>'wait', 'label'=>'Wait' ),
		array( 'value'=>'help', 'label'=>'Help' ),
		array( 'value'=>'url', 'label'=>'URL' )
	);
	
	public static function getOptionArray() {
		$options = array();
		foreach ( self::$_cursors as $el ) {
			$options[$el['value']] = $el['label'];
		}
		return $options;
	}
	
	public static function toOptionArray() {
		$options = array();
		foreach ( self::$_cursors as $el ) {
			$options[] = $el;
		}
		return $options;
	}
}

