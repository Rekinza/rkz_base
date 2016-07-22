<?php

class Thredshare_Stylesister_Model_Resource_Stylesister extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('thredshare_stylesister/stylesister', 'kinzasister_id'); //look in the config's resource par to find the "sister" entity
    }
}