<?php 

require 'Zend/Config/Ini.php';

class CitrusPay_Moto_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->getResponse()->setBody($this->getLayout()->createBlock('moto/form_pay')->toHtml());
	}
	
	private static function _generateHmacKey($data, $apiKey=null){
		$hmackey = Zend_Crypt_Hmac::compute($apiKey, "sha1", $data);
		return $hmackey;
	}

	public function paymentAction()
	{
		$txnid = "";
		$txnrefno = "";
		$TxStatus = "";
		$txnmsg = "";
		$firstName = "";
		$lastName = "";
		$email = "";
		$street1 = "";
		$city = "";
		$state = "";
		$country = "";
		$pincode = "";
		$mobileNo = "";
		$signature = "";
		$reqsignature = "";
		$data = "";
		$flag = "dataValid";
		$respdata = "";
		
		$apiKey = Mage::getStoreConfig('payment/moto/apikey');
		
		if($this->getRequest()->isPost())
		{
			$signatureFlag = Mage::getStoreConfig('payment/moto/matchSignature');
			
			$postdata = $this->getRequest()->getPost();
			
			$txnid = $postdata['TxId'];
			$data .= $txnid;
			$respdata .= "<br/><strong>Citrus Transaction Id: </strong>".$txnid;
			
			$TxStatus = $postdata['TxStatus'];
			$data .= $TxStatus;
			$respdata .= "<br/><strong>Transaction Status: </strong>".$TxStatus;
			
			$amount = $postdata['amount'];
			$data .= $amount;
			$respdata .= "<br/><strong>Amount: </strong>".$amount;
			
			$pgtxnno = $postdata['pgTxnNo'];
			$data .= $pgtxnno;
			$respdata .= "<br/><strong>PG Transaction Number: </strong>".$pgtxnno;
			
			$issuerrefno = $postdata['issuerRefNo'];
			$data .= $issuerrefno;
			$respdata .= "<br/><strong>Issuer Reference Number: </strong>".$issuerrefno;
			
			$authidcode = $postdata['authIdCode'];
			$data .= $authidcode;
			$respdata .= "<br/><strong>Auth ID Code: </strong>".$authidcode;
			
			$firstName = $postdata['firstName'];
			$data .= $firstName;
			$respdata .= "<br/><strong>First Name: </strong>".$firstName;
			
			$lastName = $postdata['lastName'];
			$data .= $lastName;
			$respdata .= "<br/><strong>Last Name: </strong>".$lastName;
			
			$pgrespcode = $postdata['pgRespCode'];
			$data .= $pgrespcode;
			$respdata .= "<br/><strong>PG Response Code: </strong>".$pgrespcode;
			
			$pincode = $postdata['addressZip'];
			$data .= $pincode;
			$respdata .= "<br/><strong>PinCode: </strong>".$pincode;
			
			$signature = $postdata['signature'];
			
			$respSignature = self::_generateHmacKey($data,$apiKey);
			
			/* Suppose a Custom parameter by name Roll Number Comes in Post Parameter.
			 * then we need to retreive the RollNumber as
			* $rollNumber = $postdata['Roll Number'];
			* For other custom parameters as well this code can be used to retreive them. */
			
			if($signature != "" && strcmp($signature, $respSignature) != 0)
			{
				$flag = "dataTampered";
			}
			$txMsg = $postdata['TxMsg'];
			$respdata .= "<br/><strong>Transaction Message: </strong>".$txMsg;
			$txnGateway = $_POST['TxGateway'];
			$respdata .= "<br/><strong>Transaction Gateway: </strong>".$txnGateway;
			$issuerCode = $_POST['issuerCode'];
			$respdata .= "<br/><strong>Issuer Code: </strong>".$issuerCode;
			$paymentMode = $_POST['paymentMode'];
			$respdata .= "<br/><strong>Payment Mode: </strong>".$paymentMode;
			$maskedCardNumber = $_POST['maskedCardNumber'];
			$respdata .= "<br/><strong>Card Number: </strong>".$maskedCardNumber;
			$cardType = $_POST['cardType'];
			$respdata .= "<br/><strong>Card Type: </strong>".$cardType;
			Mage::log("Response received is ".$TxStatus);
			Mage::log("Response Message is ".$txMsg);
			Mage::log("Response signature recieved is ".$signature);
			Mage::log("Response signature generated is ".$respSignature);
			if($TxStatus == 'SUCCESS')
			{
				if($signatureFlag == 'Y')
				{
					if($flag != "dataValid")
					{	
						Mage::getSingleton('core/session')->addSuccess('Response signature does not match. You might have received tampered data');
					}else{
						//Mage::getSingleton('core/session')->addSuccess($respdata);
					}
				}
				$this->_redirect('checkout/onepage/success');
			}
			else
			{
				if($signatureFlag == 'Y')
				{
					if($flag != "dataValid")
					{
						Mage::getSingleton('checkout/session')->setErrorMessage("Request Signature Mismatch: Error occured while processing your transaction. Please note down the transaction
						reference number<br / > <strong>Error:</strong> $txMsg");
					}
					else
					{
						Mage::getSingleton('checkout/session')->setErrorMessage("Error occured while processing your transaction. Please note down the transaction
								reference number<br / > <strong>Error:</strong> $txMsg <br/>".$respdata);
					}
				}
				else
				{
					Mage::getSingleton('checkout/session')->setErrorMessage("Error occured while processing your transaction. Please note down the transaction
							reference number<br / > <strong>Error:</strong> $txMsg <br/>".$respdata);
				}
				$this->_redirect('checkout/onepage/failure');
			}
		}
		Mage::log("Transaction END from Citruspay");
	}

}