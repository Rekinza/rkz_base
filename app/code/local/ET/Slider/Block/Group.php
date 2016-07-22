<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Group extends Mage_Core_Block_Template {
	
	public $_default_template = 'et/slider/default.phtml';
	
	public function _prepareLayout() {
		return parent::_prepareLayout();
	}

	public function getGroup() {
		if( !$this->hasData('group') ) {
			$group_id = $this->getData('group_id');
			if ( $group_id ){
				$group = Mage::getModel('slider/group')->load( $group_id );
				if ( $group && $group->getId() == $group_id ){
					$this->setData('group', $group);
				}
			}
		}
		return $this->getData('group');
	}

	public function _beforeToHtml(){
		if ( !$this->getTemplate() ){
			$this->setTemplate($this->_default_template);
		}
		return parent::_beforeToHtml();
	}
}