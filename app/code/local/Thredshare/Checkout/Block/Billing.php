<?php
class Thredshare_Checkout_Block_Billing extends Mage_Checkout_Block_Onepage_Billing{
public function getCountryHtmlSelect($type)
    {
	
	return preg_replace('/\<option\ value\=\"\"\ \>\ <\/option\>/',"",parent::getCountryHtmlSelect($type));
/*
   $countryList = Mage::getResourceModel('directory/country_collection')
                ->loadData()
                ->toOptionArray(false);
$html="";
  $html.= '<select class="required-entry" id="county" name="county">';         
      foreach ($countryList as $key => $value) 
	  {
		$html.=	'<option value="'.$value['label'].'">'.$value['label'].'</option>';
	  }
		$html.='</select>';
	return $html;
	*/
	}
}
?>
