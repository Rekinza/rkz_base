<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_Pattern {
	public function toOptionArray(){
	    $_urlmedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		return array(
			array('value'=>'pattern1', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern1.png" />')),
			array('value'=>'pattern2', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern2.png" />')),
			array('value'=>'pattern3', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern3.png" />')),
			array('value'=>'pattern4', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern4.png" />')),
			array('value'=>'pattern5', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern5.png" />')),
			array('value'=>'pattern6', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern6.png" />')),
			array('value'=>'pattern7', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern7.png" />')),
		    array('value'=>'pattern8', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern8.png" />')),
		    array('value'=>'pattern9', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern9.png" />')),
			array('value'=>'pattern10', 'label'=>Mage::helper('oxynic')->__('<img src="'.$_urlmedia.'et/pattern/pattern10.png" />'))
		);
	}
}
