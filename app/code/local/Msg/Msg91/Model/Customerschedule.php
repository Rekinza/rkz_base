<?php

class Msg_Msg91_Model_CustomerSchedule extends Mage_Core_Model_Abstract {

  
    public function _construct() {
        $this->_init('msg91/customerschedule');
    }

  
    /**
     * Runs schedule and sends  Notification 
     */
    public function run() {
	Mage::log('customerschedulephp',null,'system.log',true);
        $this->_prepare();
    }
    /**
     *Initilize and sends msg91 Notification message to customer
     *  
     * @param type $history
     * @return boolean
     */

    protected function _sendMsg91($customerCollection) {
	  if (!$this->getHelper()->isEnabled()) {
            return;
        }
	Mage::log('sendpushstart',null,'system.log',true);
	foreach($customerCollection as $custom)
	{

	$cust = Mage::getModel('msg91/customnotification')->load($custom);
       $message = $cust->getContent();
    echo   $routeVal= $cust->getRoute();
    $customer_names=unserialize($cust->getCustomerGroup());
    $groupname=array();
    foreach($customer_names as $customerGroupId){
	$groupname[] = Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();

    }
	$customers = Mage::getModel("customer/customer")->getCollection()->addFieldToFilter('group_id', array('in' => $customer_names));
	 $to=array();
	 foreach($customers as $customer){
	   $customer_email=$customer->getEmail();
	   
	    $cust1 = Mage::getModel("customer/customer");
	    $cust1->setWebsiteId(1);
	    $cust1->loadByEmail($customer_email);
	       $to[] =$cust1->getPrimaryBillingAddress()->getTelephone();
	 }
      
	$to=array_unique($to);
	
	 $phone=implode(',',$to);
	  $groups=implode(',',$groupname);
	 $params = array(
                            'phone' => $phone,
			    'route'=>$routeVal,
                        );
         $messageType = 'schedule';
	 $senderId = $this->getHelper()->getSenderId();
                    $this->getHelper()->sendShedulerSms($message,$params);
                    $chars = $this->getHelper()->getChars();
                    $length = $this->getHelper()->getLength();
                    $smsStatus = $this->getHelper()->getSmsStatus();
                    try {
                        Mage::getModel('msg91/notificationlog')
                                ->setSentDate(Mage::getModel('core/date')->timestamp(time()))
                                ->setSenderId($senderId)
                                ->setTo($phone)
                                ->setRecipient($groups)
                                ->setChars($chars)
                                ->setLength($length)
                                ->setStatus($smsStatus)
				->setScheduledAt($cust->getScheduledAt())
                                ->save();
                    }
                    catch (Exception $e) {
                        echo $e;
                    }
	}
        return true;

        }
                  
           
    
    
  
    /**
     * Return Quote collection as object
     * 
     * @return object
     */
    protected function _getCustomerCollection() {

        $resource = Mage::getSingleton('core/resource');
        $customerCollection = Mage::getModel('msg91/customnotification')->getCollection();
	$customerCollection->addFieldToFilter('is_active', '1');
	
	$customer_arr= array();
	$timeFirst='';
	$timeSecond='';
	
	foreach($customerCollection as $customer)
	{
	$sch = $customer->getScheduledAt();
	   $timeSecond=date('Y-m-d H:i',strtotime($sch));echo "<br/>";
	    $timeFirst= Mage::getModel('core/date')->date('Y-m-d H:i');echo "<br/>";
	
	if($timeFirst==$timeSecond)
	{
	$customer_arr[] = $customer->getId();
	}
	}
        return $customer_arr;
    }

    
    /**
     * Prepares history creation method.
     */
    protected function _prepare() {

    $customerCollection=$this->_getCustomerCollection();
    $this->_sendMsg91($customerCollection);
  
    }
    public function getHelper()
    {
        return Mage::helper('msg91');
    }
    /**
     * Cleans cache
     * @param $time
     */
    public function setLastExecuted($time) {
        Mage::getConfig()->saveConfig(self::LAST_EXECUTED_PATH, $time);
        Mage::getConfig()->cleanCache();
    }

}
