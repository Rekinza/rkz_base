<?php
class Msg_Msg91_Adminhtml_TestmessageController extends Mage_Adminhtml_Controller_Action
{
 
    public function indexAction()
    {
        $this->loadLayout();

        $this->renderLayout();
    }
    public function testAction()
    {
	 $mobile = $this->getRequest()->getPost('mobile');
	 $message = $this->getRequest()->getPost('message');
	 $params = array('test_message' => $message,
                          'phone' => $mobile,
                        );
	 $senderId = $this->getHelper()->getSenderId();
	 $title="Test Message";
	 $messageType = "test";
	 $helper = Mage::helper('msg91/data');
         $result = $helper->sendSms($messageType,$params);
	 $countryCode = Mage::getStoreConfig('general/country/default');
	 $country = Mage::getModel('directory/country')->loadByCode($countryCode);
	 $chars = $this->getHelper()->getChars();
                    $length = $this->getHelper()->getLength();
		    if($result){
                    $smsStatus =1;
		    }
		    else{
			 $smsStatus =2;
		    }
                    $apistatus= $this->getHelper()->getApiStatus();
                    try {
                        Mage::getModel('msg91/log')
                                ->setSentDate(Mage::getModel('core/date')->timestamp(time()))
                                ->setTitle($title)
                                ->setSenderId($senderId)
                                ->setTo($mobile)
                                ->setRecipient('admin')
                                ->setMsgContent($message)
                                ->setCountry($country)
                                ->setChars(strlen($message))
                                ->setLength('1')
                                ->setStatus($smsStatus)
                                ->setApiStatus($apistatus)
                                ->save();
                    }
                    catch (Exception $e) {
                        echo $e;
                    }
                
	if($result)
	{
	    
	    
	  Mage::getSingleton('core/session')->addSuccess('YOUR MSG SEND SUCCESS');	
	}
	else
	{
	 Mage::getSingleton('core/session')->addError('YOUR MSG FAILED');
	}

	
	$this->_redirectReferer();

    }
  
     public function getHelper()
    {
        return Mage::helper('msg91');
    }
} 

