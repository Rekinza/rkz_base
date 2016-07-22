<?php

class Thredshare_Stylesister_Model_Stylesister extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init("thredshare_stylesister/stylesister"); //changed from "stylesister" to "sister" because that is the entity name
    }


    public function saveSisters($mailedvendorid, $CustomerEmail)
	{

			echo $mailedvendorid." ".$CustomerEmail;

			$date = Mage::getModel('core/date')->date("m/d/Y", $date);
			 $this->setDate($date);
			
			 $this->setCustomerEmail($CustomerEmail);
			 $this->setSellerMap($mailedvendorid."|");
			 $this->save();
	
	
	}


}

?>