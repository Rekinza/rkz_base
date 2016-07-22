<?php
class Loginradius_Sociallogin_Model_Source_SharingVerticalAlignment
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'top_left', 'label' => __('Top Left'));
        $result[] = array('value' => 'top_right', 'label' => __('Top Right'));
        $result[] = array('value' => 'bottom_left', 'label' => __('Bottom Left'));
        $result[] = array('value' => 'bottom_right', 'label' => __('Bottom Right'));
        return $result;
    }
}