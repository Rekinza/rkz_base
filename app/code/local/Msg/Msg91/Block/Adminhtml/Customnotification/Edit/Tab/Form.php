<?php

class  Msg_Msg91_Block_Adminhtml_Customnotification_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
   protected function _prepareForm()
   {
   if (Mage::registry('rule_data'))
	{
            $data = Mage::registry('rule_data')->getData();
	    $data['customer_group']= unserialize($data['customer_group']);
        }
	else
	{
            $data = array();
        }
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('rule_form', array('legend'=>Mage::helper('msg91')->__('Rule Information')));
	//End
	
	 $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('msg91')->__('Rule Name'),
          'class'     => 'required-entry',
          'name'      => 'name'
		));
	  
	 $fieldset->addField('scheduled_at', 'date',array(
          'name'      =>    'scheduled_at', /* should match with your table column name where the data should be inserted */
          'time'      =>    true,       
          'format'    =>    $this->escDates(),
          'label'     =>    Mage::helper('msg91')->__('Data and Time'),
          'image'     =>    $this->getSkinUrl('images/grid-cal.gif'),
	  ));
	 $afterElementHtml = '<p class="nm"><small>' .'Availabe Vars {{firstname}},{{email}}'. '</small></p>';
	 $fieldset->addField('content', 'textarea', array(
          'label'     => Mage::helper('msg91')->__('Message'),
          'name'      => 'content',
	  'style'     => 'width:270px; height:50px;',
	   'after_element_html' => $afterElementHtml,
        ));

	   

	 $fieldset->addField('is_active', 'select', array(
	  'label'     => Mage::helper('msg91')->__('Status'),
	  'name'      => 'is_active',
	  'values'    => array(
	  array(
	    'value'     => 1,
	  'label'     => Mage::helper('msg91')->__('Enabled'),
	  ),
	  
	  array(
	    'value'     => 0,
	    'label'     => Mage::helper('msg91')->__('Disabled'),
	  ),
	  ),
	  ));
	  $fieldset->addField('route', 'select', array(
	  'label'     => Mage::helper('msg91')->__('Route'),
	  'name'      => 'route',
	  'values'    =>$this->getRouteList(),
	  ));
	  $afterElementHtml2 = '<p class="nm"><small><a href="http://help.msg91.com/article/10-how-to-choose-sender-id">' .'http://help.msg91.com/article/10-how-to-choose-sender-id'. '</a></small></p>';
	 $fieldset->addField('sender_id', 'text', array(
	  'label'     => Mage::helper('msg91')->__('Sender Id'),
	  'class'     => 'required-entry',
	  'name'      => 'sender_id',
	  'after_element_html' => $afterElementHtml2,
	  ));

   $customer_group = $fieldset->addField('customer_list', 'multiselect', array(
        'label'     => Mage::helper('msg91')->__('Customer Groups'),
        'name'      => 'customer_list',
	'index'      => 'customer_list',
	'id'       => 'customer_list',
	'values' => $this->getCustomerList(),
        ));
     
 $customer_group->setAfterElementHtml("<script type=\"text/javascript\">
              document.addEventListener('DOMContentLoaded', function () {                
                });
        
                var cars = [".$this->getCustomerSelectedList()."];
		$('customer_list').setValue(cars);
		
            function getcustomer(selectElement){	    
	    var selectedids = $('customer_list').getValue(); 
               var value=selectElement.value;
                var reloadurl = '". $this->getUrl('msg91/adminhtml_customnotification/customer')  . "?name='+value+'&selid='+selectedids;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (nameform) {
		    $('customer_list').append(nameform.responseText);
		     $('#customer_list').values('Searching...');                  
                 },
                    onComplete: function(nameform) {
		    $('customer_list').update(nameform.responseText);
		    return false;
                    }
                });
          
            }
	    
	 function getcustomer1(selectElement){
	 
	   // alert($('#customer_list').val());
	    
	    var ss1 = selectElement;
	    alert(ss1);
	    var ss = $('idval').value;
	    var ss12 = ss+','+ss1;	    
	    $('idval').value = ss12;
	    alert(ss12);
	  //  alert($('idval').value);
	  }
        </script>
	<input type='hidden' value='0' id='idval' name='idval'>
	
	");
	  
	 $form->setValues($data);

        return parent::_prepareForm();
      }
      private function escDates() {
	      return 'yyyy-MM-dd HH:mm:ss';   
      }

      public function getCustomerList() {
	     $customer =  Mage::getModel('customer/group')->getCollection();
	     foreach ( $customer as $key => $user ){
		 $customer_list[$key]["value"] = $user->getData("customer_group_id");
		 $customer_list[$key]["label"] = $user->getData("customer_group_code");
	     }
	     return array_values($customer_list);
	 }
	 
	   public function getCustomerSelectedList() {
	     $Id = $this->getRequest()->getParam('id');
	     $select_customer = Mage::getModel('msg91/customnotification')
	       ->getCollection()
	       ->addFieldToFilter('rule_id',$Id);
	       foreach($select_customer as $select)
	       {
		 $selec_customer = unserialize($select->getCustomerGroup()); 
	       }
	     $value = implode(",",$selec_customer); 
	     return $value;
	 }
	  protected static $_options;
      public function getRouteList(){
	       $url="https://control.msg91.com/api/getUserDetails.php?";
      $authKey=Mage::helper('msg91')->getAuthkeyUrl();
      $params="authkey={$authKey}&response=json";
      $result=Mage::helper('msg91')->file_get_contents_curl($url, $params);
      $xml = json_decode($result, true) or die("Error: Cannot create object");
      $result = ((array) $xml);
      if (!self::$_options) {
            self::$_options = array();
            }
       foreach($result['routes'] as $route){
                self::$_options[]=array(
                    'label' => Mage::helper('msg91')->__($route['name']),
                    'value' => $route['id'],
                );              
        }
        return self::$_options;
	 }
}
?>

