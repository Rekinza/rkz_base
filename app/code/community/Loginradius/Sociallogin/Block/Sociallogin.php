<?php
class Loginradius_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template {
  public function getPopusScriptUrl() {
    $jsPath = Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/js/popup.js', array('_area' => 'frontend'));
    $cssPath = Mage::getDesign()->getSkinUrl('Loginradius/Sociallogin/css/popup.css', array('_area' => 'frontend'));

    echo '<script src="' . $jsPath . '"></script>';
    echo '<link rel = "stylesheet" href="' . $cssPath . '" media = "all" />';

  }

  public function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getSociallogin() {
    if (!$this->hasData('sociallogin')) {
      $this->setData('sociallogin', Mage::registry('sociallogin'));
    }
    return $this->getData('sociallogin');
  }

  public function user_is_already_login() {
    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
      return TRUE;
    }
    return FALSE;
  }

  public function getApikey() {
    return trim(Mage::getStoreConfig('sociallogin_options/apiSettings/apikey'));
  }

  public function getApiSecret() {
    return trim(Mage::getStoreConfig('sociallogin_options/apiSettings/apisecret'));
  }

  public function getLoginRedirectOption() {
    return Mage::getStoreConfig('sociallogin_options/basicSettings/redirectAfterLogin');
  }

  public function getRegistrationRedirectOption() {
    return Mage::getStoreConfig('sociallogin_options/basicSettings/redirectAfterRegistration');
  }

  public function getCustomLoginRedirectOption() {
    return Mage::getStoreConfig('sociallogin_options/basicSettings/customUrlLogin');
  }

  public function getCustomRegistrationRedirectOption() {
    return Mage::getStoreConfig('sociallogin_options/basicSettings/customUrlLogin');
  }

  public function getRegistrationCallBack() {
    return Mage::getStoreConfig('sociallogin_options/basicSettings/customUrlRegistration');
  }

  public function iconSize() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/iconSize');
  }

  public function iconsPerRow() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/iconsPerRow');
  }

  public function backgroundColor() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/backgroundColor');
  }

  public function getShowDefault() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/showdefault');
  }

  public function getAboveLogin() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/aboveLogin');
  }

  public function getBelowLogin() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/belowLogin');
  }

  public function getAboveRegister() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/aboveRegister');
  }

  public function getBelowRegister() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/belowRegister');
  }

  public function getEmailRequired() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/emailrequired');
  }

  public function verificationText() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/verificationText');
  }

  public function getPopupText() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/popupText');
  }

  public function getPopupError() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/popupError');
  }

  public function notifyUser() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyUser');
  }

  public function notifyUserText() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyUserText');
  }

  public function notifyAdmin() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyAdmin');
  }

  public function notifyAdminText() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/notifyAdminText');
  }

  public function getAvatar($id) {
    $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
    $SocialLoginTbl = Mage::getSingleton('core/resource')->getTableName("sociallogin");
    $select = $socialLoginConn->query("select avatar from $SocialLoginTbl where entity_id = '$id' limit 1");
    if ($rowArray = $select->fetch()) {
      if (($avatar = trim($rowArray['avatar'])) != "") {
        return $avatar;
      }
    }
    return FALSE;
  }

  public function getProfileFieldsRequired() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/profileFieldsRequired');
  }

  public function updateProfileData() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/updateProfileData');
  }

  public function getLinking() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/socialLinking');
  }

  public function isDebuggingOn() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/debugMode');
  }

  public function horizontalSharingTheme() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalSharingTheme');
  }

  public function horizontalShareSuccess() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareSuccess');
  }

  public function horizontalSharingProviders() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalSharingProvidersHidden');
  }

  public function horizontalShareProduct() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareProduct');
  }

  /* Get horizontal sharing optionS */

  public function horizontalCounterProviders() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalCounterProvidersHidden');
  }

  public function verticalSharingTheme() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalSharingTheme');
  }

  public function verticalAlignment() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalAlignment');
  }

  public function verticalSharingProviders() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalSharingProvidersHidden');
  }

  public function verticalCounterProviders() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalCounterProvidersHidden');
  }

  public function verticalShareProduct() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareProduct');
  }

  public function verticalShareSuccess() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareSuccess');
  }

  public function offset() {
    return 100;
  }

  public function getProfileResult($ApiSecrete) {
    if (isset($_REQUEST['token'])) {
      $ValidateUrl = "http://hub.loginradius.com/userprofile.ashx?token=" . $_REQUEST['token'] . "&apisecrete=" . trim($ApiSecrete);
      return $this->getApiCall($ValidateUrl);
    }
  }

  public function getSocialLoginContainer() {
    $lrKeys = $this->getApiKeys();
    $AdvancedSettings = $this->getAdvancedSettings();
    $lrSettings = array_merge($lrKeys, $AdvancedSettings);
    $UserAuth = $this->getApiValidation(trim($lrSettings['apikey']), trim($lrSettings['apisecret']));
    echo '<div class="block" style="margin-top:15px"><div class="block-title"><strong><span>' . __('Social Login') . '</span></strong></div><div class="block-content"><p class="empty">';
    if ($lrKeys['apikey'] == "" && $lrKeys['apikey'] == "apisecret") {
      echo '<p style ="color:red;">' . $this->__('To activate your plugin, please log in to LoginRadius and get API Key & Secret. Web') . ': <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
    }
    elseif ($UserAuth == FALSE) {
      echo '<p style ="color:red;">' . $this->__('Your LoginRadius API Key and Secret is not valid, please correct it or contact LoginRadius support at') . ' <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b></p>';
    }
    else {
      echo '<div style="margin:5px"><div style="margin-bottom:5px">' . trim($this->getLoginRadiusTitle()) . '</div><div class="interfacecontainerdiv"></div></div>';
    }
    echo '</p></div></div>';
  }

  public function getApiKeys() {
    return Mage::getStoreConfig('sociallogin_options/apiSettings');
  }

  public function getAdvancedSettings() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings');
  }

  public function getApiValidation($ApiKey, $ApiSecrete) {
    if (!empty($ApiKey) && !empty($ApiSecrete) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiKey) && preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $ApiSecrete)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }


  // Extra methods to remove

  public function getLoginRadiusTitle() {
    return Mage::getStoreConfig('sociallogin_options/advancedSettings/loginradius_title');
  }

  protected function _construct() {
    parent::_construct();
    if ($this->horizontalShareEnable() == "1" || $this->verticalShareEnable() == "1") {
      $this->setTemplate('sociallogin/socialshare.phtml');
    }
  }

  public function horizontalShareEnable() {
    return Mage::getStoreConfig('sociallogin_options/horizontalSharing/horizontalShareEnable');
  }

  public function verticalShareEnable() {
    return Mage::getStoreConfig('sociallogin_options/verticalSharing/verticalShareEnable');
  }

}