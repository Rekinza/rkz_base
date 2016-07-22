<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Model_Resource_Position_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
    /**
     * Define collection model
     */
    protected function _construct(){
        $this->_init('megamenu/position');
    }
}