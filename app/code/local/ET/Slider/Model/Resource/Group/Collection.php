<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_Resource_Group_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('slider/group');
    }
}