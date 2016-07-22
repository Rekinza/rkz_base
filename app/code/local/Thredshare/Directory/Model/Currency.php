<?php

class Thredshare_Directory_Model_Currency extends Mage_Directory_Model_Currency{ 
 public function format($price, $options = array(), $includeContainer = true, $addBrackets = false)
    {		
        return $this->formatPrecision($price, 0, $options, $includeContainer, $addBrackets);
    }
}
?>