<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Model_Resource_Caption extends Mage_Core_Model_Resource_Db_Abstract {
    public function _construct() {
        // Note that the id refers to the key field in your database table.
        $this->_init('slider/caption', 'id');
    }
}