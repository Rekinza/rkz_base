<?php

/* require 'Zend/Config/Ini.php'; */


class CitrusPay_Moto_Block_Form_Pay extends Mage_Core_Block_Abstract
{
	protected function _toHtml()
	{
		Mage::log("Transaction START");
		$form = new Varien_Data_Form();

		$apiKey = Mage::getStoreConfig('payment/moto/apikey');
		Mage::log("API Key is ".$apiKey);
		$vanityUrl = Mage::getStoreConfig('payment/moto/vanityurl');
		$ordid = Mage::getSingleton('checkout/session')->getLastOrderId();
		$order = Mage::getModel('sales/order')->load($ordid);
		$txnid = $order->getIncrementId();
		Mage::log("Merchant Transaction ID is ".$txnid);
		$amount = $order->getGrandTotal();
		Mage::log("Amount is ".$amount);
		$baseurl = Mage::getBaseUrl();
		$returnUrl = $baseurl."moto/index/payment/";
		$currency = "INR";
		$shippingAddress = $order->getShippingAddress();
		$nameFlag = Mage::getStoreConfig('payment/moto/passName');
		$emailFlag = Mage::getStoreConfig('payment/moto/passEmail');
		$addFlag = Mage::getStoreConfig('payment/moto/passAddress');
		$phoneFlag = Mage::getStoreConfig('payment/moto/passPhone');
		$firstName = $shippingAddress->getData('firstname');
		Mage::log("firstName is ".$firstName);
		$lastName = $shippingAddress->getData('lastname');
		Mage::log("lastName is ".$lastName);
		$email = $shippingAddress->getData('email');
		Mage::log("email is ".$email);
		$street = $shippingAddress->getData('street');
		Mage::log("street address is ".$street);
		$city = $shippingAddress->getData('city');
		Mage::log("City is ".$city);
		$postcode = $shippingAddress->getData('postcode');
		Mage::log("Zip Code is ".$postcode);
		$region = $shippingAddress->getData('region');
		Mage::log("State Code is ".$region);
		$country = $shippingAddress->getData('country');
		Mage::log("Country is ".$country);
		$telephone = $shippingAddress->getData('telephone');
		Mage::log("Phone Number is ".$telephone);

		$data = "$vanityUrl$amount$txnid$currency";
		Mage::log("Signature String is ".$data);

		$signatureData = self::_generateHmacKey($data, $apiKey);
		Mage::log("Signature Key generated via HMAC is ".$signatureData);
		$env = Mage::getStoreConfig('payment/moto/environment');
		Mage::log("Connecting to environment ".$env);
		$config = new Zend_Config_Ini(dirname(__FILE__)."/../../config/citruspay.ini","$env");
		$sslPage = $config->paymentUrl;
		
		Mage::log("Connecting to URL ".$sslPage.$vanityUrl);

		$form->setAction("$sslPage$vanityUrl")
		->setId("CPForm")
		->setName('citruspay_checkout')
		->setMethod('POST')
		->setUseContainer(true);
		$form->addField('Security Signature', 'hidden', array('name'=>'secSignature', 'value'=>$signatureData));
		$form->addField('TransactionId', 'hidden', array('name'=>'merchantTxnId', 'value'=>$txnid));
		$form->addField('Amount', 'hidden', array('name'=>'orderAmount', 'value'=>$amount));
		$form->addField('Currency', 'hidden', array('name'=>'currency', 'value'=>$currency));
		if($nameFlag == 'Y')
		{
			$form->addField('firstName', 'hidden', array('name'=>'firstName', 'value'=>$firstName));
			$form->addField('lastName', 'hidden', array('name'=>'lastName', 'value'=>$lastName));
			Mage::log("Passing name to ssl page ");
		}
		if($emailFlag == 'Y')
		{
			$form->addField('email', 'hidden', array('name'=>'email', 'value'=>$email));
			Mage::log("Passing email to ssl page ");
		}
		if($addFlag == 'Y')
		{
			$form->addField('addressStreet1', 'hidden', array('name'=>'addressStreet1', 'value'=>$street));
			$form->addField('addressCity', 'hidden', array('name'=>'addressCity', 'value'=>$city));
			$form->addField('addressZip', 'hidden', array('name'=>'addressZip', 'value'=>$postcode));
			$form->addField('addressState', 'hidden', array('name'=>'addressState', 'value'=>$region));
			$form->addField('addressCountry', 'hidden', array('name'=>'addressCountry', 'value'=>$country));
			Mage::log("Passing address details to ssl page ");
		}
		if($phoneFlag == 'Y')
		{
			$form->addField('phoneNumber', 'hidden', array('name'=>'phoneNumber', 'value'=>$telephone));
			Mage::log("Passing phone number to ssl page ");
		}
		if(self::isCOD()){
			$form->addField('COD', 'hidden', array('name'=>'COD', 'value'=>'Yes'));
			Mage::log("COD Setting is set to Y");
		}
		$form->addField('ReturnUrl', 'hidden',
				array('name'=>'returnUrl',
					 'value'=>$returnUrl));
		//$form->addField('ReqTime', 'hidden', array('name'=>'reqtime', 'value'=> (time()*1000)));
		$html = '<html><body>';
		$html.= $form->toHtml();
		$html.= '<script type="text/javascript">document.getElementById("CPForm").submit();</script>';
		$html.= '</body></html>';

		return $html;
	}

	private static function _generateHmacKey($data, $apiKey=null){
		$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
		return $hmackey;
	}
	
	private static function isCOD(){
		$iscod = Mage::getStoreConfig('payment/moto/showcod');
		if($iscod == 'Y'){
			return true;
		}
		return false;
	}


}