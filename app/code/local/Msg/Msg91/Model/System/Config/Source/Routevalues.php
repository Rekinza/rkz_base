<?php
 
class Msg_Msg91_Model_System_Config_Source_Routevalues
{
      protected static $_options;

    public function toOptionArray()
    {
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
