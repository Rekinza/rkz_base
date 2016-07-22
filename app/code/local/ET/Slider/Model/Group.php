<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_Group extends Mage_Core_Model_Abstract {
	/**
	 * Define resource model
	 */
	protected function _construct() {
        parent::_construct();
        $this->_init('slider/group');
    }
	/**
	 * If object is new adds creation date
	 *
	 * @return ET_Slider_Model_Group
	 */
	protected function _beforeSave() {
		parent::_beforeSave();
		if($this->isObjectNew('group')) {
			$this->setData('created_at', Varien_Date::now() );
		}
		return $this;
	}
}