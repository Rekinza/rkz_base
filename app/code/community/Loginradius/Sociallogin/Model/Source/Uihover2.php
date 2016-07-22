<?php
class Loginradius_Sociallogin_Model_Source_Uihover2
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'varien', 'label' => __('Use') . ' "http_varien_client"');
        $result[] = array('value' => 'curl', 'label' => __('Use') . ' cURL');
        $result[] = array('value' => 'fopen', 'label' => __('Use') . ' FSOCKOPEN');
        return $result;
    }
}