<?php

    class Msg_Msg91_Adminhtml_CustomnotificationController extends Mage_Adminhtml_Controller_action
    {
        
	
	public function indexAction() {
         $this->loadLayout();
		//$this->_initAction();
               // $this->_addContent($this->getLayout()->createBlock('msg91/adminhtml_customnotification'));
		$this->renderLayout();
	}
	 public function newAction() {
	    $this->_forward('edit');
    }
    
    public function editAction() {

	
	$id     = $this->getRequest()->getParam('id');
	$model  = Mage::getModel('msg91/customnotification')->load($id);
	
	if ($model->getId() || $id == 0) {
	    $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
	    if (!empty($data)) {
		    $model->setData($data);
	    }
	    Mage::register('rule_data', $model);
	    $this->loadLayout();			 
	    // We can create block here or in the xml file.(see test.xml file)
	    $this->_addContent($this->getLayout()->createBlock('msg91/adminhtml_customnotification_edit'))
	    ->_addLeft($this->getLayout()->createBlock('msg91/adminhtml_customnotification_edit_tabs')); 
	    $this->renderLayout();
	}
	else {
	    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customnotification')->__('error'));
	    $this->_redirect('*/*/');
	}
    }
    
    public function saveAction(){
	$data = $this->getRequest()->getParams();
	
	$str = serialize($data['customer_list']);
	$data['customer_group'] = $str;
	if($data['is_active']){
	  foreach($data['customer_list'] as $customerGroupId){

	$groupname[] = Mage::getModel('customer/group')->load($customerGroupId)->getCustomerGroupCode();

    }
    $customers = Mage::getModel("customer/customer")->getCollection()->addFieldToFilter('group_id', array('in' => $data['customer_list']));
	 $to=array();
	 foreach($customers as $customer){
	   $customer_email=$customer->getEmail();
	   
	    $cust1 = Mage::getModel("customer/customer");
	    $cust1->setWebsiteId(1);
	    $cust1->loadByEmail($customer_email);
	     $addressId = $cust1->getPrimaryBillingAddress();
	    if(is_object($addressId)){
	      
	       $to_num=$cust1->getPrimaryBillingAddress()->getTelephone();
	      
	       $groups=implode(',',$groupname);
	       $params = array(
			    'email'=>$customer->getEmail(),
			    'firstname'=>$cust1->getFirstname(),
			    'lastname'=>$cust1->getLastname(),
                            'phone' => $to_num,
			    'schtime'=>$data['scheduled_at'],
			    'route'=>$data['route'],
			    'sender_id'=>$data['sender_id']
                        );
	       if(!in_array($to_num,$to)){
		 $this->getHelper()->sendShedulerSms($data['content'],$params);
	    
	     $senderId = $data['sender_id'];
                    $chars = $this->getHelper()->getChars();
                    $length = $this->getHelper()->getLength();
                    $smsStatus = $this->getHelper()->getSmsStatus();
			$apistatus= $this->getHelper()->getApiStatus();
                    try {
                        Mage::getModel('msg91/notificationlog')
                                ->setSentDate(Mage::getModel('core/date')->timestamp(time()))
                                ->setSenderId($senderId)
                                ->setTo($to_num)
                                ->setRecipient($cust1->getFirstname())
                                ->setChars($chars)
                                ->setLength($length)
                                ->setStatus($smsStatus)
				->setScheduledAt($data['scheduled_at'])
				->setApiStatus($apistatus)
                                ->save();
                    }
                    catch (Exception $e) {
                        echo $e;
                    }
		     $to[]=$cust1->getPrimaryBillingAddress()->getTelephone();  }
	 }

	$to=array_unique($to);
	
	 $phone=implode(',',$to);
	  $groups=implode(',',$groupname);
	 
	}
	$model = Mage::getModel('msg91/customnotification');
	try
	{
	$model->setData($data)
		->setId($this->getRequest()->getParam('id'));
	$model->save();
	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('msg91')->__('Data was successfully saved'));
			Mage::getSingleton('adminhtml/session')->setFormData(false);
	if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/');
                }
 
	return;
	} catch (Exception $e) {
	    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	    Mage::getSingleton('adminhtml/session')->setFormData($data);
	    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	    return; 
	}   
    }
    }
    
    public function massDeleteAction() {
	
	$zoneIds = $this->getRequest()->getParam('rules');
	
	if(!is_array($zoneIds)) {
	    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
	}else{
	    try {
		    foreach ($zoneIds as $zoneId)
		    {
			$zonetransition = Mage::getModel('msg91/customnotification')->load($zoneId);
			$zonetransition->delete(); 
		    }
		    Mage::getSingleton('adminhtml/session')->addSuccess(
		    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($zoneIds)));
		    
		} catch (Exception $e)
		{
		    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
	}
	$this->_redirect('*/*/index');
    }
    public function deleteAction(){
	if($data = Mage::app()->getRequest()->getParams('id')) {
	    try{
		$zonetransition = Mage::getModel('msg91/customnotification')->load($data['id']);
		$zonetransition->delete();
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Details deleted successfully...!'));
	    }catch(Expection $e){
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customnotification')->__("Can't delete details, try again...!"));
	    }
	}
	$this->_redirect('*/*/');
    }
	
   public function customerAction(){
     $html = "";
       $data = Mage::app()->getRequest()->getParams();
       $name = $data["name"];
     //  $data["selid"] = "2,3,8";
       $value = explode(",",$data["selid"]);
       
      $customerCollection = Mage::getModel('customer/customer')->getCollection();
      $customerCollection->addNameToSelect();
      $customerCollection->addAttributeToSelect(array(
      'dob', 'firstname', 'lastname', 'email'
      ));
    $customerCollection->addAttributeToFilter('entity_id',$value);
 foreach ($customerCollection as $customer) {
    $getIdValue = $customer->getData();
   
     $html .= '<option selected="selected" value="'. $getIdValue['entity_id']. '" >'. $customer->getName() .'</option>';
 }
 
       $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToFilter('firstname', array('like' => '%'.$name.'%'));
       
       foreach ($collection->getData() as $val) {
	    if(!in_array($val['entity_id'],$value)) {
            $html .= '<option value="'. $val['entity_id']. '" >'. $val['firstname'] .'</option>';     }       
       }
	echo $html;
	exit;
}
    
    public function _isAllowed()
    {
        return true;
    }
       public function getHelper()
    {
        return Mage::helper('msg91');
    }
	
}
