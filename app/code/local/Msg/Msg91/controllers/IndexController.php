<?php
class Msg_Msg91_IndexController extends Mage_Core_Controller_Front_Action
{
   
       public function file_get_contents_curl($url, $params)
    {
        Mage::log('Gateway url:'.$url, null, 'msg91.log', true);
        Mage::log('Params:'.$params, null, 'msg91.log', true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        if(!$data)
        {
            throw new Exception(curl_error($ch));
        }
        
        Mage::log('Response:'.$data, null, 'msg91.log', true);
        
        curl_close($ch);
        return $data;
    }
}
