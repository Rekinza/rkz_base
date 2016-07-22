<?php
class Loginradius_Sociallogin_Model_Source_Uihover
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'same', 'label' => __('Same Page'));
        $result[] = array('value' => 'account', 'label' => __('Account Page'));
        $result[] = array('value' => 'index', 'label' => __('Home Page'));
        $result[] = array('value' => 'custom', 'label' => __('Custom URL'));
        return $result;
    }
}