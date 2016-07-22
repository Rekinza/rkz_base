<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tab_Slider extends Mage_Adminhtml_Block_Widget_Form {
	protected static $_default = array(
		'fill_mode' => 0,
		'lazy_loading' => 1,
		'start_index' => 0,
		'auto_play' => 1,
		'loop' => 1,
		'hwa' => 1,
		'auto_play_steps' => 1,
		'auto_play_interval' =>3000,
		'pause_on_hover' => 3,
		'arrow_key_navigation' => 0,
		'slide_duration' => 500,
		'slide_easing' => '$JssorEasing$.$EaseOutQuad',
		'min_drag_offset_to_slide' => 20,
		'slide_width' => '',
		'slide_height' => '',
		'slide_spacing' => 0,
		'display_pieces' => 1,
		'parking_position' => 0,
		'ui_search_mode' => 1,
		'play_orientation' => 1,
		'drag_orientation' => 0
	);
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getGroupData() ){
			$current = Mage::getSingleton('adminhtml/session')->getGroupData();
			Mage::getSingleton('adminhtml/session')->getGroupData(null);
		} elseif( Mage::registry('group_data') ) {
			$current = Mage::registry('group_data');
		}
		
		// check and set default values
		foreach (self::$_default as $prop => $df){
			$val = $current->getData($prop);
			if ( is_null($val) ){
				// var_dump("set default value for " . $prop . ' is ' . $df);
				$current->setData($prop, $df);
			}
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('slider_opt_form', array('legend'=>Mage::helper('slider')->__('Sldier Options')));
		
		$fieldset->addField('group_form_fill_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('Fill Mode'),
				'name'      => 'fill_mode',
				'after_element_html'   => '<p class="note"><span>The way to fill image in slide</span></p>',
				'value' 	=> $current->getData('fill_mode'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('Stretch') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Contain') ),
						2 => array( 'value'=>2, 'label'=>Mage::helper('slider')->__('Cover') ),
						4 => array( 'value'=>4, 'label'=>Mage::helper('slider')->__('Actual Size') ),
				)
		));
		
		$fieldset->addField('group_form_lazy_load', 'select', array(
				'label'     => Mage::helper('slider')->__('Lazyload'),
				'name'      => 'lazy_load',
				'value' 	=> $current->getData('lazy_load'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') ),
				)
		));
		
		$fieldset->addField('group_form_start_index', 'text', array(
				'label' => Mage::helper('slider')->__('Start Index'),
				'name' => 'start_index',
				'value' => $current->getData('start_index')
		));
		
		
		$fieldset->addField('group_form_auto_play', 'select', array(
				'label'     => Mage::helper('slider')->__('Autoplay'),
				'name'      => 'auto_play',
				'value'     => $current->getData('auto_play'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') ),
				)
		));
		$fieldset->addField('group_form_auto_play_steps', 'text', array(
				'label' => Mage::helper('slider')->__('Autoplay Steps'),
				'name' => 'auto_play_steps',
				'value' => $current->getData('auto_play_steps')
		));
		$fieldset->addField('group_form_auto_play_interval', 'text', array(
				'label' => Mage::helper('slider')->__('Autoplay Interval'),
				'name' => 'auto_play_interval',
				'value' => $current->getData('auto_play_interval')
		));
		
		$fieldset->addField('group_form_loop', 'select', array(
				'label'     => Mage::helper('slider')->__('Loop'),
				'name'      => 'loop',
				'after_element_html'   => '<p class="note"><span>Enable loop(circular) of carousel or not</span></p>',
				'value' 	=> $current->getData('loop'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') ),
				)
		));
		
		$fieldset->addField('group_form_hwa', 'select', array(
				'label'     => Mage::helper('slider')->__('HWA'),
				'name'      => 'hwa',
				'after_element_html'   => '<p class="note"><span>Enable hardware acceleration or not</span></p>',
				'value' 	=> $current->getData('hwa'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') ),
				)
		));
		
		$fieldset->addField('group_form_pause_on_hover', 'select', array(
				'label'     => Mage::helper('slider')->__('Pause On Hover'),
				'name'      => 'pause_on_hover',
				'after_element_html'   => '<p class="note"><span>Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device</span></p>',
				'value' 	=> $current->getData('pause_on_hover'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No Pause') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Pause for Desktop') ),
						2 => array( 'value'=>2, 'label'=>Mage::helper('slider')->__('Pause for Touch Devices') ),
						3 => array( 'value'=>3, 'label'=>Mage::helper('slider')->__('Both Desktop and Touch Devices') ),
				)
		));
		
		$fieldset->addField('group_form_arrow_key_navigation', 'select', array(
				'label'     => Mage::helper('slider')->__('Arrow Key Navigation'),
				'name'      => 'arrow_key_navigation',
				'after_element_html'   => '<p class="note"><span>Allows keyboard (arrow key) navigation or not</span></p>',
				'value' 	=> $current->getData('arrow_key_navigation'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Yes') ),
				)
		));
		
		$fieldset->addField('group_form_slide_duration', 'text', array(
				'label' => Mage::helper('slider')->__('Slide Duration'),
				'name' => 'slide_duration',
				'value' => $current->getData('slide_duration')
		));
		
		$fieldset->addField('group_form_slide_easing', 'select', array(
				'label' => Mage::helper('slider')->__('Slide Easing'),
				'name' => 'slide_easing',
				'value' => $current->getData('slide_easing'),
				'values' => Mage::getModel('slider/system_config_source_easing')->toOptionArray()
		));
		
		$fieldset->addField('group_form_min_drag_offset_to_slide', 'text', array(
				'label' => Mage::helper('slider')->__('Min Drag Offset to Slide'),
				'name' => 'min_drag_offset_to_slide',
				'value' => $current->getData('min_drag_offset_to_slide')
		));
		
		$fieldset->addField('group_form_slide_width', 'text', array(
				'label' => Mage::helper('slider')->__('Slide Width'),
				'name' => 'slide_width',
				'value' => $current->getData('slide_width')
		));
		
		$fieldset->addField('group_form_slide_height', 'text', array(
				'label' => Mage::helper('slider')->__('Slide Height'),
				'name' => 'slide_height',
				'value' => $current->getData('slide_height')
		));
		
		$fieldset->addField('group_form_slide_spacing', 'text', array(
				'label' => Mage::helper('slider')->__('Slide Spacing'),
				'name' => 'slide_spacing',
				'value' => $current->getData('slide_spacing')
		));
		
		$fieldset->addField('group_form_display_pieces', 'text', array(
				'label' => Mage::helper('slider')->__('Display Pieces'),
				'name' => 'display_pieces',
				'value' => $current->getData('display_pieces')
		));
		
		$fieldset->addField('group_form_parking_position', 'text', array(
				'label' => Mage::helper('slider')->__('Parking Position'),
				'name' => 'parking_position',
				'value' => $current->getData('parking_position')
		));
		
		$fieldset->addField('group_form_ui_search_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('UI Search Mode'),
				'name'      => 'ui_search_mode',
				'after_element_html'   => '<p class="note"><span>The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc)</span></p>',
				'value' 	=> $current->getData('ui_search_mode'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=>Mage::helper('slider')->__('Parellel') ),
						1 => array( 'value'=>1, 'label'=>Mage::helper('slider')->__('Recursive') )
				)
		));
		
		$fieldset->addField('group_form_play_orientation', 'select', array(
				'label' => Mage::helper('slider')->__('Play Orientation'),
				'name' => 'play_orientation',
				'value' => $current->getData('play_orientation'),
				'values'    => array(
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') )
				)
		));
		
		$fieldset->addField('group_form_drag_orientation', 'select', array(
				'label' => Mage::helper('slider')->__('Drag Orientation'),
				'name' => 'drag_orientation',
				'after_element_html' => '<p class="note"><span>Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either (Note that the $DragOrientation should be the same as $PlayOrientation when $DisplayPieces is greater than 1, or parking position is not 0)</span></p>',
				'value' => $current->getData('drag_orientation'),
				'values'    => array(
						0 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('No Drag') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') ),
						3 => array( 'value'=>3, 'label'=> Mage::helper('slider')->__('Either') )
				)
		));
		
		return parent::_prepareForm();
	}
}