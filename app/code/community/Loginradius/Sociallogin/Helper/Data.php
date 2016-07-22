<?php
class Loginradius_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Returns whether the Enabled config variable is set to true
     *
     * @return bool
     */
    public function isSocialloginEnabled()
    {
        if (Mage::getStoreConfig('sociallogin_options/messages/enabled') == '1') {
            return true;
        }
        return false;
    }

    public function isCurlEnabled()
    {
        return function_exists('curl_version') ? '1' : '0';
    }

    public function isCustomerAlreadyLoggedIn()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return true;
        }
        return false;
    }

}