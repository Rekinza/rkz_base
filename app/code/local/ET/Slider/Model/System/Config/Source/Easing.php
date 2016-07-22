<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_System_Config_Source_Easing {
	protected $_easing = array(
		'$EaseLinear' => '$JssorEasing$.$EaseLinear',
		'$EaseGoBack' => '$JssorEasing$.$EaseGoBack',
		'$EaseSwing' => '$JssorEasing$.$EaseSwing',
		'$EaseInQuad' => '$JssorEasing$.$EaseInQuad',
		'$EaseOutQuad' => '$JssorEasing$.$EaseOutQuad',
		'$EaseInOutQuad' => '$JssorEasing$.$EaseInOutQuad',
		'$EaseInCubic' => '$JssorEasing$.$EaseInCubic',
		'$EaseOutCubic' => '$JssorEasing$.$EaseOutCubic',
		'$EaseInOutCubic' => '$JssorEasing$.$EaseInOutCubic',
		'$EaseInQuart' => '$JssorEasing$.$EaseInQuart',
		'$EaseOutQuart' => '$JssorEasing$.$EaseOutQuart',
		'$EaseInOutQuart' => '$JssorEasing$.$EaseInOutQuart',
		'$EaseInQuint' => '$JssorEasing$.$EaseInQuint',
		'$EaseOutQuint' => '$JssorEasing$.$EaseOutQuint',
		'$EaseInOutQuint' => '$JssorEasing$.$EaseInOutQuint',
		'$EaseInSine' => '$JssorEasing$.$EaseInSine',
		'$EaseOutSine' => '$JssorEasing$.$EaseOutSine',
		'$EaseInOutSine' => '$JssorEasing$.$EaseInOutSine',
		'$EaseInExpo' => '$JssorEasing$.$EaseInExpo',
		'$EaseOutExpo' => '$JssorEasing$.$EaseOutExpo',
		'$EaseInOutExpo' => '$JssorEasing$.$EaseInOutExpo',
		'$EaseInCirc' => '$JssorEasing$.$EaseInCirc',
		'$EaseOutCirc' => '$JssorEasing$.$EaseOutCirc',
		'$EaseInOutCirc' => '$JssorEasing$.$EaseInOutCirc',
		'$EaseInElastic' => '$JssorEasing$.$EaseInElastic',
		'$EaseOutElastic' => '$JssorEasing$.$EaseOutElastic',
		'$EaseInOutElastic' => '$JssorEasing$.$EaseInOutElastic',
		'$EaseInBack' => '$JssorEasing$.$EaseInBack',
		'$EaseOutBack' => '$JssorEasing$.$EaseOutBack',
		'$EaseInOutBack' => '$JssorEasing$.$EaseInOutBack',
		'$EaseInBounce' => '$JssorEasing$.$EaseInBounce',
		'$EaseOutBounce' => '$JssorEasing$.$EaseOutBounce',
		'$EaseInOutBounce' => '$JssorEasing$.$EaseInOutBounce',
		'$EaseInWave' => '$JssorEasing$.$EaseInWave',
		'$EaseOutWave' => '$JssorEasing$.$EaseOutWave',
		'$EaseOutJump' => '$JssorEasing$.$EaseOutJump',
		'$EaseInJump' => '$JssorEasing$.$EaseInJump'
	);
	public function toOptionArray() {
		$options = array();
		foreach ($this->_easing as $n => $e){
			$options[] = array( 'value' => $e, 'label' => $n );
		}
		return $options;
	}
	
	
}
