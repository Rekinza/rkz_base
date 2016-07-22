<?php
class Loginradius_Sociallogin_Model_Source_Loginpopup
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => '1', 'label' => __('Yes') . '<br/>');
        $result[] = array('value' => '0', 'label' => __('No') . '<br/>');
        return $result;
    }
}