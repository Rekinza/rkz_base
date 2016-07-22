<?php
date_default_timezone_set('Asia/Kolkata');
class Thredshare_Stylesister_StylesisterController extends Mage_Core_Controller_Front_Action{

public function indexAction() {
	
	echo "nikita style";
Mage::getModel("thredshare_stylesister/stylesister")->saveSisters(1234, 5678);

//Mage::getModel("thredshare_stylesister/stylesister")->hello();

}



}