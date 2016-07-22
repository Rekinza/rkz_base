<?php
class Loginradius_Sociallogin_Model_Source_twitterRecipients
{
    public function toOptionArray()
    {
        $result = array();
        $result[] = array('value' => 'selected', 'label' => 'Let the user pick<br/>');
        $result[] = array('value' => 'all', 'label' => 'All contacts');
        return $result;
    }
}