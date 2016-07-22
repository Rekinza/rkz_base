<?php

class Thredshare_Stylesister_Model_Resource_Stylesister_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('thredshare_stylesister/stylesister'); 

 	}

 }
 
//the folder-name inside the resource folder was made according to the name of the modelclasses and not w.r.t config file
           