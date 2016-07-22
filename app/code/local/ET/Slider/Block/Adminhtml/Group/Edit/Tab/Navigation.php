<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Edit_Tab_Navigation extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		
		if( Mage::getSingleton('adminhtml/session')->getGroupData() ){
			$current = Mage::getSingleton('adminhtml/session')->getGroupData();
			Mage::getSingleton('adminhtml/session')->getGroupData(null);
		} elseif( Mage::registry('group_data') ) {
			$current = Mage::registry('group_data');
		}
		
		// check and set default values
		$bullet_chance_to_show = $current->getData('bullet_chance_to_show');
		if ( is_null($bullet_chance_to_show) ){
			$current->setData('bullet_chance_to_show', 2);
		}
		
		$bullet_action_mode = $current->getData('bullet_action_mode');
		if ( is_null($bullet_action_mode) ){
			$current->setData('bullet_action_mode', 1);
		}
		
		$bullet_auto_center = $current->getData('bullet_auto_center');
		if ( is_null($bullet_auto_center) ){
			$current->setData('bullet_auto_center', 0);
		}
		
		$bullet_steps = $current->getData('bullet_steps');
		if ( is_null($bullet_steps) ){
			$current->setData('bullet_steps', 1);
		}
		
		$bullet_lanes = $current->getData('bullet_lanes');
		if ( is_null($bullet_lanes) ){
			$current->setData('bullet_lanes', 1);
		}
		
		$bullet_spacing_x = $current->getData('bullet_spacing_x');
		if ( is_null($bullet_spacing_x) ){
			$current->setData('bullet_spacing_x', 0);
		}
		
		$bullet_spacing_y = $current->getData('bullet_spacing_y');
		if ( is_null($bullet_spacing_y) ){
			$current->setData('bullet_spacing_y', 0);
		}
		
		$bullet_orientation = $current->getData('bullet_orientation');
		if ( is_null($bullet_orientation) ){
			$current->setData('bullet_orientation', 1);
		}
		
		
		$arrow_chance_to_show = $current->getData('arrow_chance_to_show');
		if ( is_null($arrow_chance_to_show) ){
			$current->setData('arrow_chance_to_show', 2);
		}
		$arrow_steps = $current->getData('arrow_steps');
		if ( is_null($arrow_steps) ){
			$current->setData('arrow_steps', 1);
		}
		
		
		$thumbnail_chance_to_show = $current->getData('thumbnail_chance_to_show');
		if ( is_null($thumbnail_chance_to_show) ){
			$current->setData('thumbnail_chance_to_show', 2);
		}
		
		$thumbnail_loop = $current->getData('thumbnail_loop');
		if ( is_null($thumbnail_loop) ){
			$current->setData('thumbnail_loop', 1);
		}
		
		$thumbnail_action_mode = $current->getData('thumbnail_action_mode');
		if ( is_null($thumbnail_action_mode) ){
			$current->setData('thumbnail_action_mode', 1);
		}
		
		$thumbnail_auto_center = $current->getData('thumbnail_auto_center');
		if ( is_null($thumbnail_auto_center) ){
			$current->setData('thumbnail_auto_center', 3);
		}
		
		$thumbnail_lanes = $current->getData('thumbnail_lanes');
		if ( is_null($thumbnail_lanes) ){
			$current->setData('thumbnail_lanes', 1);
		}
		
		$thumbnail_spacing_x = $current->getData('thumbnail_spacing_x');
		if ( is_null($thumbnail_spacing_x) ){
			$current->setData('thumbnail_spacing_x', 0);
		}
		
		$thumbnail_spacing_y = $current->getData('thumbnail_spacing_y');
		if ( is_null($thumbnail_spacing_y) ){
			$current->setData('thumbnail_spacing_y', 0);
		}
		
		$thumbnail_display_pieces = $current->getData('thumbnail_display_pieces');
		if ( is_null($thumbnail_display_pieces) ){
			$current->setData('thumbnail_display_pieces', 1);
		}
		
		$thumbnail_parking_position = $current->getData('thumbnail_parking_position');
		if ( is_null($thumbnail_parking_position) ){
			$current->setData('thumbnail_parking_position', 0);
		}
		
		$thumbnail_orientation = $current->getData('thumbnail_orientation');
		if ( is_null($thumbnail_orientation) ){
			$current->setData('thumbnail_orientation', 1);
		}
		
		$thumbnail_disable_drag = $current->getData('thumbnail_disable_drag');
		if ( is_null($thumbnail_disable_drag) ){
			$current->setData('thumbnail_disable_drag', 0);
		}
		
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset1 = $form->addFieldset('bullet_navigation_opt_form', array('legend'=>Mage::helper('slider')->__('Bullet Navigation Options')));
		
		$fieldset1->addField('group_form_bullet_chance_to_show', 'select', array(
				'label'     => Mage::helper('slider')->__('Chance to Show'),
				'name'      => 'bullet_chance_to_show',
				'value' 	=> $current->getData('bullet_chance_to_show'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('Never') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Mouse Over') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Always') ),
				)
		));
		
		$fieldset1->addField('group_form_bullet_action_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('Action Mode'),
				'name'      => 'bullet_action_mode',
				'value' 	=> $current->getData('bullet_action_mode'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('None') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('by Click') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('by Mouse Over') ),
						3 => array( 'value'=>3, 'label'=> Mage::helper('slider')->__('Both') )
				)
		));
		
		$fieldset1->addField('group_form_bullet_auto_center', 'select', array(
				'label'     => Mage::helper('slider')->__('Auto Center'),
				'name'      => 'bullet_auto_center',
				'value' 	=> $current->getData('bullet_auto_center'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('None') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') ),
						3 => array( 'value'=>3, 'label'=> Mage::helper('slider')->__('Both') )
				)
		));
		
		$fieldset1->addField('group_form_bullet_steps', 'text', array(
				'label'     => Mage::helper('slider')->__('Steps'),
				'name'      => 'bullet_steps',
				'value' => $current->getData('bullet_steps')
		));
		
		$fieldset1->addField('group_form_bullet_lanes', 'text', array(
				'label'     => Mage::helper('slider')->__('Lanes'),
				'name'      => 'bullet_lanes',
				'value' => $current->getData('bullet_lanes')
		));
		
		$fieldset1->addField('group_form_bullet_spacing_x', 'text', array(
				'label'     => Mage::helper('slider')->__('Spacing X (px)'),
				'name'      => 'bullet_spacing_x',
				'value' => $current->getData('bullet_spacing_x')
		));
		
		$fieldset1->addField('group_form_bullet_spacing_y', 'text', array(
				'label'     => Mage::helper('slider')->__('Spacing Y (px)'),
				'name'      => 'bullet_spacing_y',
				'value' => $current->getData('bullet_spacing_y')
		));
		
		$fieldset1->addField('group_form_bullet_orientation', 'select', array(
				'label'     => Mage::helper('slider')->__('Chance to Show'),
				'name'      => 'bullet_orientation',
				'value' 	=> $current->getData('bullet_orientation'),
				'values'    => array(
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') )
				)
		));
	
		$fieldset2 = $form->addFieldset('arrow_navigation_opt_form', array('legend'=>Mage::helper('slider')->__('Arrow Navigation Options')));

		$fieldset2->addField('group_form_arrow_chance_to_show', 'select', array(
				'label'     => Mage::helper('slider')->__('Chance to Show'),
				'name'      => 'arrow_chance_to_show',
				'value' 	=> $current->getData('arrow_chance_to_show'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('Never') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Mouse Over') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Always') ),
				)
		));
		
		$fieldset2->addField('group_form_arrow_steps', 'text', array(
				'label'     => Mage::helper('slider')->__('Steps'),
				'name'      => 'arrow_steps',
				'value' => $current->getData('arrow_steps')
		));
		
		$fieldset3 = $form->addFieldset('thumbnail_navigation_opt_form', array('legend'=>Mage::helper('slider')->__('Thumbnail Navigation Options')));
		
		$fieldset3->addField('group_form_thumbnail_chance_to_show', 'select', array(
				'label'     => Mage::helper('slider')->__('Chance to Show'),
				'name'      => 'thumbnail_chance_to_show',
				'value' 	=> $current->getData('thumbnail_chance_to_show'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('Never') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Mouse Over') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Always') ),
				)
		));
		
		$fieldset3->addField('group_form_thumbnail_loop', 'select', array(
				'label'     => Mage::helper('slider')->__('Loop'),
				'name'      => 'thumbnail_loop',
				'value' 	=> $current->getData('thumbnail_loop'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Yes') )
				)
		));
		
		$fieldset3->addField('group_form_thumbnail_action_mode', 'select', array(
				'label'     => Mage::helper('slider')->__('Action Mode'),
				'name'      => 'thumbnail_action_mode',
				'value' 	=> $current->getData('thumbnail_action_mode'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('None') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('by Click') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('by Mouse Over') ),
						3 => array( 'value'=>3, 'label'=> Mage::helper('slider')->__('Both') )
				)
		));
		
		$fieldset3->addField('group_form_thumbnail_auto_center', 'select', array(
				'label'     => Mage::helper('slider')->__('Auto Center'),
				'name'      => 'thumbnail_auto_center',
				'value' 	=> $current->getData('thumbnail_auto_center'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('None') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') ),
						3 => array( 'value'=>3, 'label'=> Mage::helper('slider')->__('Both') )
				)
		));
		
		$fieldset3->addField('group_form_thumbnail_lanes', 'text', array(
				'label'     => Mage::helper('slider')->__('Lanes'),
				'name'      => 'thumbnail_lanes',
				'value' => $current->getData('thumbnail_lanes')
		));
		
		$fieldset3->addField('group_form_thumbnail_spacing_x', 'text', array(
				'label'     => Mage::helper('slider')->__('Spacing X (px)'),
				'name'      => 'thumbnail_spacing_x',
				'value' => $current->getData('thumbnail_spacing_x')
		));
		
		$fieldset3->addField('group_form_thumbnail_spacing_y', 'text', array(
				'label'     => Mage::helper('slider')->__('Spacing Y (px)'),
				'name'      => 'thumbnail_spacing_y',
				'value' => $current->getData('thumbnail_spacing_y')
		));
		
		$fieldset3->addField('group_form_thumbnail_display_pieces', 'text', array(
				'label'     => Mage::helper('slider')->__('Display Pieces'),
				'name'      => 'thumbnail_display_pieces',
				'value' => $current->getData('thumbnail_display_pieces')
		));
		
		$fieldset3->addField('group_form_thumbnail_parking_position', 'text', array(
				'label'     => Mage::helper('slider')->__('Parking Position'),
				'name'      => 'thumbnail_parking_position',
				'value' => $current->getData('thumbnail_parking_position')
		));
		
		$fieldset3->addField('group_form_thumbnail_orientation', 'select', array(
				'label'     => Mage::helper('slider')->__('Orientation'),
				'name'      => 'thumbnail_orientation',
				'value' 	=> $current->getData('thumbnail_orientation'),
				'values'    => array(
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Horizontal') ),
						2 => array( 'value'=>2, 'label'=> Mage::helper('slider')->__('Vertical') )
				)
		));
		
		$fieldset3->addField('group_form_thumbnail_disable_drag', 'select', array(
				'label'     => Mage::helper('slider')->__('Disable Drag'),
				'name'      => 'thumbnail_disable_drag',
				'value' 	=> $current->getData('thumbnail_disable_drag'),
				'values'    => array(
						0 => array( 'value'=>0, 'label'=> Mage::helper('slider')->__('No') ),
						1 => array( 'value'=>1, 'label'=> Mage::helper('slider')->__('Yes') )
				)
		));
		
		return parent::_prepareForm();
	}
}