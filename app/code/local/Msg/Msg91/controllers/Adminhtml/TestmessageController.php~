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
	$messageType = "test";
	$helper = Mage::helper('msg91/data');
        $result = $helper->sendSms($messageType,$params);
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
  
 
} 

