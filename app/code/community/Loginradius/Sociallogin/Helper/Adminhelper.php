<?php
/**
 * Created by PhpStorm.
 * User: nyaconcept
 * Date: 11/17/14
 * Time: 12:33 PM
 */

class Loginradius_Sociallogin_Helper_AdminHelper extends Mage_Core_Helper_Abstract
{
    public function loginRadiusApiClient($ValidateUrl, $data)
    {
        if ($this->getApiMethod()) {
            $response = $this->curlApiMethod($ValidateUrl, $data);
        } else {
            $response = $this->fsockopenApiMethod($ValidateUrl, $data);
        }
        $message = isset($response->Messages[0]) ? trim($response->Messages[0]) : '';
        switch ($message) {
            case 'API_KEY_NOT_VALID':
                $results['status'] = "error";
                $results['message'] = JText::_('COM_SOCIALLOGIN_SAVE_SETTING_ERROR_ONE') . " <a href='http://www.loginradius.com' target='_blank'>LoginRadius</a>";
                break;
            case 'API_SECRET_NOT_VALID':
                $results['status'] = "error";
                $results['message'] = JText::_('COM_SOCIALLOGIN_SAVE_SETTING_ERROR_TWO') . " <a href='http://www.loginradius.com' target='_blank'>LoginRadius</a>";
                break;
            case 'API_KEY_NOT_FORMATED':
                $results['status'] = "error";
                $results['message'] = JText::_('COM_SOCIALLOGIN_SAVE_SETTING_ERROR_THREE');
                break;
            case 'API_SECRET_NOT_FORMATED':
                $results['status'] = "error";
                $results['message'] = JText::_('COM_SOCIALLOGIN_SAVE_SETTING_ERROR_FOUR');
                break;
            default:
                $results['status'] = "message";
                $results['message'] = JText::_('COM_SOCIALLOGIN_SETTING_SAVED');
                break;
        }
        return $results;
    }

    private function fsockopenApiMethod($ValidateUrl, $data)
    {
        if (!empty($data)) {
            $options = array('http' => array('method' => 'POST', 'timeout' => 15, 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => $data));
            $context = stream_context_create($options);
        } else {
            $context = null;
        }
        $jsonResponse = @file_get_contents($ValidateUrl, false, $context);

        if (strpos(@$http_response_header[0], "400") !== false
            || strpos(@$http_response_header[0], "401") !== false
            || strpos(@$http_response_header[0], "403") !== false
            || strpos(@$http_response_header[0], "404") !== false
            || strpos(@$http_response_header[0], "500") !== false
            || strpos(@$http_response_header[0], "503") !== false
        ) {
            return JTEXT::_('COM_SOCIALLOGIN_SERVICE_AND_TIMEOUT_ERROR');
        } else {
            return json_decode($jsonResponse);
        }
    }

}