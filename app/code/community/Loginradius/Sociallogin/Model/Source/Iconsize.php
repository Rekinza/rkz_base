<?php
class Loginradius_Sociallogin_Model_Source_Iconsize
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'medium', 'label' => __('Medium'));
        $result[] = array('value' => 'small', 'label' => __('Small'));
        return $result;
    }
}