<?php
/**
 * Created by PhpStorm.
 * User: nyaconcept
 * Date: 11/17/14
 * Time: 10:53 AM
 */
class Loginradius_Sociallogin_Model_Observer extends Mage_Core_Helper_Abstract {
  public function adminSystemConfigChangedSection() {
    $post = Mage::app()->getRequest()->getPost();
    foreach ($post['groups']['apiSettings']['fields'] as $apis) {
      if (isset($apis['inherit']) && $apis['inherit']) {
        $result['message'] = '';
        $result['status'] = 'Success';
        return $result;
      }
    }
    $this->getAllVariables('basic');

  }

  public function getAllVariables($action) {
    $function = 'loginradius_' . $action . '_setting_array';
    $settings = $this->$function();
    $responce = $this->getval($settings, $action);
    $session = Mage::getSingleton('adminhtml/session');
    $session->getMessages(TRUE);
    if ($responce['status'] != 'Success') {
      throw new Exception($responce['message']);
    }
  }

  public function getval($fieldArrays, $action) {
    $post = Mage::app()->getRequest()->getPost();
    $count = 1;
    $string = '';
    foreach ($fieldArrays as $fieldId => $fieldArray) {
      foreach ($fieldArray as $fieldValue) {
        $settings[$fieldValue] = isset($post['groups'][$fieldId]['fields'][$fieldValue]['value']) ? $post['groups'][$fieldId]['fields'][$fieldValue]['value'] : '';
      }

      $string .= $this->loginradius_get_string_format($count, $fieldArray, $settings);
      $count++;
    }

    $result['message'] = '';
    $result['status'] = 'Success';
    $validateUrl = 'https://api.loginradius.com/api/v2/app/validate?apikey=' . rawurlencode($settings['apikey']) . '&apisecret=' . rawurlencode($settings['apisecret']);

    $data = array(
      'addon'         => 'Magento: ' . Mage::getVersion(),
      'version'       => '4.0.0',
      'agentstring'   => $_SERVER["HTTP_USER_AGENT"],
      'clientip'      => $_SERVER["REMOTE_ADDR"],
      'configuration' => $string
    );

    return $this->loginradius_save_setting_on_server($validateUrl, $data);
  }

  function loginradius_get_string_format($tabNo, $array, $settings) {
    $string = "~" . $tabNo . "#";
    for ($i = 0; $i < count($array); $i++) {
      if (in_array($array[$i], array('appid', 'appkey'))) {
        continue;
      }
      elseif (is_numeric($settings[$array[$i]])) {
        $string .= '|' . $settings[$array[$i]];
      }
      elseif (@unserialize($settings[$array[$i]])) {
        $string .= '|' . json_encode(@unserialize($settings[$array[$i]]));
      }
      elseif (is_string($settings[$array[$i]])) {
        $string .= '|"' . $settings[$array[$i]] . '"';
      }
    }
    return $string . '|';
  }

  function loginradius_save_setting_on_server($url, $data) {
    $result['status'] = 'Error';
    $loginradiusObgect = new Loginradius_Sociallogin_Helper_LoginRadiusSDK();
    $responce = json_decode($loginradiusObgect->accessLoginradiusApi($url, http_build_query($data)));
    $status = isset($responce->Status) ? $responce->Status : FALSE;
    $result['message'] = isset($responce->Messages[0]) ? $responce->Messages[0] : 'An error ocCureD';
    if ($status) {
      $result['message'] = '';
      $result['status'] = 'Success';
    }
    else {
      if ($result['message'] == 'API_KEY_NOT_FORMATED') {
        $result['message'] = 'LoginRadius API key is not correct.';
      }
      elseif ($result['message'] == 'API_SECRET_NOT_FORMATED') {
        $result['message'] = 'LoginRadius API Secret key is not correct.';
      }
      elseif ($result['message'] == 'API_KEY_NOT_VALID') {
        $result['message'] = 'LoginRadius API key is not valid.';
      }
      elseif ($result['message'] == 'API_SECRET_NOT_VALID') {
        $result['message'] = 'LoginRadius API Secret key is not valid.';
      }
    }
    return $result;
  }

  public function loginradius_basic_setting_array() {
    return array(
      'apiSettings'       => array('apikey', 'apisecret'),
      'basicSettings'     => array('redirectAfterLogin', 'customLogin', 'redirectAfterRegistration', 'customRegistration'),
      'advancedSettings'  => array('loginradius_title', 'iconSize', 'iconsPerRow', 'backgroundColor', 'showdefault', 'aboveLogin', 'belowLogin', 'aboveRegister', 'belowRegister', 'emailrequired', 'verificationText', 'popupText', 'popupError', 'notifyUser', 'notifyUserText', 'notifyAdmin', 'notifyAdminText', 'profileFieldsRequired', 'updateProfileData', 'socialLinking', 'debugMode'),
      'horizontalSharing' => array('horizontalShareEnable', 'horizontalSharingTheme', 'horizontalAlignment', 'horizontalSharingProvidersHidden', 'horizontalCounterProvidersHidden', 'horizontalShareProduct', 'horizontalShareSuccess'),
      'verticalSharing'   => array('verticalShareEnable', 'verticalSharingTheme', 'verticalAlignment', 'verticalSharingProvidersHidden', 'verticalCounterProvidersHidden', 'verticalShareProduct', 'verticalShareSuccess')
    );
  }

  function loginradius_get_array($fieldArray) {
    $settings = array();
    foreach ($fieldArray as $fieldKey => $fieldValues) {
      foreach ($fieldValues as $fieldValue) {
        $settings[] = $fieldValue;
      }
    }
    return $settings;
  }
}