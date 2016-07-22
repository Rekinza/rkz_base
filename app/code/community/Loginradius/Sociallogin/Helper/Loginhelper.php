<?php


class Loginradius_Sociallogin_Helper_Loginhelper extends Mage_Core_Helper_Abstract {
  public function loginRadiusRead($table, $handle, $params, $result = FALSE) {
    $socialLoginConn = Mage::getSingleton('core/resource')->getConnection('core_read');
    $Tbl = $this->getMazeTable($table);
    $customerTable = $this->getMazeTable('customer_entity');
    $websiteId = Mage::app()->getWebsite()->getId();
    $storeId = Mage::app()->getStore()->getId();
    $query = "";
    switch ($handle) {
      case "email exists pop1":
        $query = "select entity_id from $Tbl where email = '" . $params[0] . "' and website_id = $websiteId and store_id = $storeId";
        break;
      case "get_user_by_social_id":
        $query = "SELECT $Tbl.entity_id, verified from $Tbl JOIN $customerTable ON $customerTable.entity_id = $Tbl.entity_id WHERE $Tbl.sociallogin_id = '" . $params[0] . "'";
        break;
      case "get_user_from_customer_entity":
        $query = "select entity_id from $Tbl where entity_id = " . $params[0] . " and website_id = $websiteId";
        break;
      case "email_already_exists":
        $query = "select * from $Tbl where email = '" . $params[0] . "' and website_id = $websiteId";
        break;
      case "email exists sl":
        $query = "select verified,sociallogin_id from $Tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
        break;
      case "provider exists in sociallogin":
        $query = "select entity_id from $Tbl where entity_id = '" . $params[0] . "' and provider = '" . $params[1] . "'";
        break;
      case "verification":
        $query = "select entity_id, provider from $Tbl where vkey = '" . $params[0] . "'";
        break;
      case "verification2":
        $query = "select entity_id from $Tbl where entity_id = " . $params[0] . " and provider = '" . $params[1] . "' and vkey != '' ";
        break;
    }
    $select = $socialLoginConn->query($query);
    if ($result) {
      return $select;
    }
    if ($select->fetch()) {
      return TRUE;
    }
    return FALSE;
  }

  public function getMazeTable($tbl) {
    $tableName = Mage::getSingleton('core/resource')->getTableName($tbl);
    return ($tableName);
  }

  /**
   * Validate url.
   */
  public function loginRadiusValidateUrl($url) {
    $validUrlExpression = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|)?[a-z0-9_\-]+[a-z0-9_\-\.]+\.[a-z]{2,4}(\/+[a-z0-9_\.\-\/]*)?$/i";
    return (bool) preg_match($validUrlExpression, $url);
  }

  public function generateRandomEmail($userObject) {
    switch ($userObject->Provider) {
      case 'twitter':
      case 'linkedin':
        $email = $userObject->ID . '@' . $userObject->Provider . '.com';
        break;
      default:
        $Email_id = substr($userObject->ID, 7);
        $Email_id2 = str_replace("/", "_", $Email_id);
        $email = str_replace(".", "_", $Email_id2) . '@' . $userObject->Provider . '.com';
        break;
    }
    return $email;
  }

  public function verifyUser($slId, $entityId, $avatar, $provider, $email,$sendAdminEmail = false,$loginRadiusUsername = '') {

    $this->blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
    $vKey = md5(uniqid(rand(), TRUE));
    $data = array();
    $data['sociallogin_id'] = $slId;
    $data['entity_id'] = $entityId;
    $data['avatar'] = $avatar;
    $data['verified'] = "0";
    $data['vkey'] = $vKey;
    $data['provider'] = $provider;
    // insert details in sociallogin table
    $this->SocialLoginInsert("sociallogin", $data);
    // send verification mail
    $message = __(Mage::helper('core')->htmlEscape(trim($this->blockObj->verificationText())));
    if ($message == "") {
      $message = __("Please click on the following link or paste it in browser to verify your email");
    }
    $message .= "<br/>" . Mage::getBaseUrl() . "sociallogin/?loginRadiusKey=" . $vKey;
    $this->loginRadiusEmail(__("Email verification"), $message, $email, $email);
    if($sendAdminEmail){
      $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
      $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
      $loginRadiusMessage = trim($this->blockObj->notifyAdminText());
      if ($loginRadiusMessage == "") {
        $loginRadiusMessage = __("New customer has been registered to your store with following details");
      }
      $loginRadiusMessage .= "<br/>" . __("Name") . " : " . $loginRadiusUsername . "<br/>" . __("Email") . " : " . $email;
      Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("New User Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);

    }
    $session = Mage::getSingleton('customer/session');
    $session->addSuccess(__('Confirmation link has been sent to your email address. Please verify your email by clicking on confirmation link.'));

    header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
    die;
  }

  public function SocialLoginInsert($lrTable, $lrInsertData, $update = FALSE, $value = '') {
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    $connection->beginTransaction();
    $sociallogin = $this->getMazeTable($lrTable);
    if (!$update) {
      $connection->insert($sociallogin, $lrInsertData);
    }
    else {
      // update query magento way
      $connection->update(
        $sociallogin, $lrInsertData, $value
      );
    }
    $connection->commit();
  }

  public function loginRadiusEmail($subject, $message, $to, $toName) {
    $storeName = Mage::app()->getStore()->getGroup()->getName();
    $mail = new Zend_Mail('UTF-8'); //class for mail
    $mail->setBodyHtml($message); //for sending message containing html code
    $mail->setFrom("Owner", $storeName);
    $mail->addTo($to, $toName);
    $mail->setSubject($subject);
    try {
      $mail->send();
    } catch (Exception $ex) {
    }
  }

  public function setTmpSession($loginRadiusPopupTxt = '', $socialLoginMsg = "", $loginRadiusShowForm = TRUE, $profileData = array(), $emailRequired = TRUE, $hideZipcode = FALSE) {
    Mage::getSingleton('core/session')->setTmpPopupTxt($loginRadiusPopupTxt);
    Mage::getSingleton('core/session')->setTmpPopupMsg($socialLoginMsg);
    Mage::getSingleton('core/session')->setTmpShowForm($loginRadiusShowForm);
    Mage::getSingleton('core/session')->setTmpProfileData($profileData);
    Mage::getSingleton('core/session')->setTmpEmailRequired($emailRequired);
    Mage::getSingleton('core/session')->setTmpHideZipcode($hideZipcode);
  }

  function setInSession($id, $socialloginProfileData) {
    $socialloginProfileData['lrId'] = $id;
    Mage::getSingleton('core/session')->setSocialLoginData($socialloginProfileData);
  }

  public function socialLoginFilterData($userObject) {
    //My code ends
    $profileDataFiltered = array();
    $profileDataFiltered['Email'] = isset($userObject->Email[0]->Value) ? $userObject->Email[0]->Value : '';
    $profileDataFiltered['Provider'] = empty($userObject->Provider) ? "" : $userObject->Provider;
    $profileDataFiltered['FirstName'] = empty($userObject->FirstName) ? "" : $userObject->FirstName;
    $profileDataFiltered['FullName'] = empty($userObject->FullName) ? "" : $userObject->FullName;
    $profileDataFiltered['NickName'] = empty($userObject->NickName) ? "" : $userObject->NickName;
    $profileDataFiltered['LastName'] = empty($userObject->LastName) ? "" : $userObject->LastName;
    if (isset($userObject->Addresses) && is_array($userObject->Addresses)) {
      foreach ($userObject->Addresses as $address) {
        if (isset($address->Address1) && !empty($address->Address1)) {
          $profileDataFiltered['Address'] = $address->Address1;
          break;
        }
      }
    }
    elseif (isset($userObject->Addresses) && is_string($userObject->Addresses)) {
      $profileDataFiltered['Address'] = isset($userObject->Addresses) && $userObject->Addresses != "" ? $userObject->Addresses : "";
    }
    else {
      $profileDataFiltered['Address'] = "";
    }
    $profileDataFiltered['PhoneNumber'] = empty($userObject->PhoneNumbers['0']->PhoneNumber) ? "" : $userObject->PhoneNumbers['0']->PhoneNumber;
    $profileDataFiltered['State'] = empty($userObject->State) ? "" : $userObject->State;
    $profileDataFiltered['City'] = empty($userObject->City) || $userObject->City == "unknown" ? "" : $userObject->City;
    $profileDataFiltered['Industry'] = empty($userObject->Positions['0']->Comapny->Name) ? "" : $userObject->Positions['0']->Comapny->Name;
    if (isset($userObject->Country->Code) && is_string($userObject->Country->Code)) {
      $profileDataFiltered['Country'] = $userObject->Country->Code;
    }
    else {
      $profileDataFiltered['Country'] = "";
    }
    $profileDataFiltered['thumbnail'] = $this->socialLoginFilterAvatar($userObject->ID, $userObject->ThumbnailImageUrl, $profileDataFiltered['Provider']);


    if (empty($profileDataFiltered['FirstName'])) {
      if (!empty($profileDataFiltered['FullName'])) {
        $profileDataFiltered['FirstName'] = $profileDataFiltered['FullName'];
      }
      elseif (!empty($profileDataFiltered['ProfileName'])) {
        $profileDataFiltered['FirstName'] = $profileDataFiltered['ProfileName'];
      }
      elseif (!empty($profileDataFiltered['NickName'])) {
        $profileDataFiltered['FirstName'] = $profileDataFiltered['NickName'];
      }
      elseif (!empty($email)) {
        $user_name = explode('@', $email);
        $profileDataFiltered['FirstName'] = str_replace("_", " ", $user_name[0]);
      }
      else {
        $profileDataFiltered['FirstName'] = $userObject->ID;
      }
    }

    $profileDataFiltered['Gender'] = (!empty($userObject->Gender) ? $userObject->Gender : '');
    if (strtolower(substr($profileDataFiltered['Gender'], 0, 1)) == 'm') {
      $profileDataFiltered['Gender'] = '1';
    }
    elseif (strtolower(substr($profileDataFiltered['Gender'], 0, 1)) == 'f') {
      $profileDataFiltered['Gender'] = '2';
    }
    else {
      $profileDataFiltered['Gender'] = '';
    }
    $profileDataFiltered['BirthDate'] = (!empty($userObject->BirthDate) ? $userObject->BirthDate : '');
    if ($profileDataFiltered['BirthDate'] != "") {
      switch ($profileDataFiltered['Provider']) {
        case 'facebook':
        case 'foursquare':
        case 'yahoo':
        case 'openid':
          break;

        case 'google':
          $temp = explode('/', $profileDataFiltered['BirthDate']); // yy/mm/dd
          $profileDataFiltered['BirthDate'] = $temp[1] . "/" . $temp[2] . "/" . $temp[0];
          break;

        case 'twitter':
        case 'linkedin':
        case 'vkontakte':
        case 'live';
          $temp = explode('/', $profileDataFiltered['BirthDate']); // dd/mm/yy
          $profileDataFiltered['BirthDate'] = $temp[1] . "/" . $temp[0] . "/" . $temp[2];
          break;
      }
    }
    return $profileDataFiltered;
  }

  public function socialLoginFilterAvatar($id, $ImgUrl, $provider) {
    $thumbnail = (!empty($ImgUrl) ? trim($ImgUrl) : '');
    if (empty($thumbnail) && ($provider == 'facebook')) {
      $thumbnail = "http://graph.facebook.com/" . $id . "/picture?type=large";
    }
    return $thumbnail;
  }

  public function loginRadiusRedirect($url) {
    ?>
    <script>
      if (window.opener) {
        window.opener.location.href = "<?php echo $url; ?>";
        window.close();
      } else {
        window.location.href = "<?php echo $url; ?>";
      }
    </script>
    <?php
    die;
  }
}