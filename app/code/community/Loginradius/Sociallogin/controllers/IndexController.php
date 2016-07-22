<?php
Mage::app('default');
function getMazeTable($tbl)
{
    $tableName = Mage::getSingleton('core/resource')->getTableName($tbl);

    return ($tableName);
}

//customer will be re-directed to this file. this file handle all token, email etc things.
class Loginradius_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action
{
    public $socialloginProfileData;
    public $blockObj;
    private $loginRadiusPopMsg;
    private $loginRadiusPopErr;

    public function indexAction()
    {
        $this->blockObj = new Loginradius_Sociallogin_Block_Sociallogin();
        $token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : '';
        if (!empty($token)) {
            $this->tokenHandle($token);

            return;
        }

        // email verification
        if (isset($_GET['loginRadiusKey']) && !empty($_GET['loginRadiusKey'])) {
            $loginRadiusVkey = trim($_GET['loginRadiusKey']);
            // get entity_id and provider of the vKey
            $result = Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "verification", array($loginRadiusVkey), true);
            if ($temp = $result->fetch()) {
                // set verified status true at this verification key
                $tempUpdate = array("verified" => '1', "vkey" => '');
                $tempUpdate2 = array("vkey = ?" => $loginRadiusVkey);
                Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", $tempUpdate, true, $tempUpdate2);

                $session = Mage::getSingleton('customer/session');
                $session->addSuccess(__('Your email has been verified. Now you can login to your account.'));
                // check if verification for same provider is still pending on this entity_id
                if (Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "verification2", array($temp['entity_id'], $temp['provider']))) {
                    $tempUpdate = array("vkey" => '');
                    $tempUpdate2 = array("entity_id = ?" => $temp['entity_id'], "provider = ?" => $temp['provider']);
                    Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", $tempUpdate, true, $tempUpdate2);
                }
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
                die;
            }
        }

        if (isset($_POST['LoginRadiusRedSliderClick'])) {
            $loginhelper = Mage::helper('sociallogin/loginhelper');
            $socialLoginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
            $session_user_id = $socialLoginProfileData['lrId'];
            $loginRadiusPopProvider = $socialLoginProfileData['Provider'];
            $loginRadiusAvatar = $socialLoginProfileData['thumbnail'];
            if (!empty($session_user_id)) {
                $loginRadiusProfileData = array();
                // address
                if (isset($_POST['loginRadiusAddress'])) {
                    $loginRadiusProfileData['Address'] = "";
                    $profileAddress = trim($_POST['loginRadiusAddress']);
                }
                // city
                if (isset($_POST['loginRadiusCity'])) {
                    $loginRadiusProfileData['City'] = "";
                    $profileCity = trim($_POST['loginRadiusCity']);
                }
                // country
                if (isset($_POST['loginRadiusCountry'])) {
                    $loginRadiusProfileData['Country'] = "";
                    $profileCountry = trim($_POST['loginRadiusCountry']);
                }
                // phone number
                if (isset($_POST['loginRadiusPhone'])) {
                    $loginRadiusProfileData['PhoneNumber'] = "";
                    $profilePhone = trim($_POST['loginRadiusPhone']);
                }
                // email
                if (isset($_POST['loginRadiusEmail'])) {
                    $email = trim($_POST['loginRadiusEmail']);
                    $select = $loginhelper->loginRadiusRead("customer_entity", "email_already_exists", array($email), true);
                    if ($rowArray = $select->fetch()) {
                        $errorMessage = $this->blockObj->getPopupError();
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $loginhelper->setTmpSession("", $errorMessage, true, $socialLoginProfileData, true);
                        } else {
                            $loginhelper->setTmpSession("", $errorMessage, true, array(), true, true);
                        }
                        $this->popupHandle();

                        return;
                    }

                    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) {
                        if ($this->blockObj->getProfileFieldsRequired() == 1) {
                            $hideZipCountry = false;
                        } else {
                            $hideZipCountry = true;
                        }
                        Mage::helper('sociallogin/loginhelper')->setTmpSession($this->blockObj->getPopupText(), $this->blockObj->getPopupError(), true, $loginRadiusProfileData, true, $hideZipCountry);
                        $this->popupHandle();

                        return;
                    }
                    // check if email already exists
                    $userId = Mage::helper('sociallogin/loginhelper')->loginRadiusRead("customer_entity", "email exists pop1", array($email), true);
                    if ($rowArray = $userId->fetch()) { // email exists
                        //check if entry exists on same provider in sociallogin table
                        $verified = Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "email exists sl", array($rowArray['entity_id'], $loginRadiusPopProvider), true);
                        if ($rowArray2 = $verified->fetch()) {
                            // check verified field
                            if ($rowArray2['verified'] == "1") {
                                // check sociallogin id
                                if ($rowArray2['sociallogin_id'] == $session_user_id) {
                                    $this->socialLoginUserLogin($rowArray['entity_id'], $rowArray2['sociallogin_id']);
                                } else {
                                    Mage::helper('sociallogin/loginhelper')->setTmpSession($this->loginRadiusPopMsg, $this->loginRadiusPopErr, true, array(), true, true);
                                    $this->popupHandle();
                                }

                                return;
                            } else {
                                // check sociallogin id
                                if ($rowArray2['sociallogin_id'] == $session_user_id) {
                                    Mage::helper('sociallogin/loginhelper')->setTmpSession("Please provide following details", "", true, $this->socialloginProfileData, false);
                                    $this->popupHandle();
                                } else {
                                    // send verification email
                                    Mage::helper('sociallogin/loginhelper')->verifyUser($session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);
                                }

                                return;
                            }
                        } else {
                            // send verification email
                            Mage::helper('sociallogin/loginhelper')->verifyUser($session_user_id, $rowArray['entity_id'], $loginRadiusAvatar, $loginRadiusPopProvider, $email);

                            return;
                        }
                    }
                }
                // validate other profile fields
                if ((isset($profileAddress) && $profileAddress == "") || (isset($profileCity) && $profileCity == "") || (isset($profileCountry) && $profileCountry == "") || (isset($profilePhone) && $profilePhone == "")) {
                    Mage::helper('sociallogin/loginhelper')->setTmpSession("", "Please fill all the fields", true, $loginRadiusProfileData, true);
                    $this->popupHandle();

                    return;
                }
                $this->socialloginProfileData = Mage::getSingleton('core/session')->getSocialLoginData();
                // assign submitted profile fields to array
                // address
                if (isset($profileAddress)) {
                    $this->socialloginProfileData['Address'] = $profileAddress;
                }
                // city
                if (isset($profileCity)) {
                    $this->socialloginProfileData['City'] = $profileCity;
                }
                // Country
                if (isset($profileCountry)) {
                    $this->socialloginProfileData['Country'] = $profileCountry;
                }
                // Phone Number
                if (isset($profilePhone)) {
                    $this->socialloginProfileData['PhoneNumber'] = $profilePhone;
                }
                // Zipcode
                if (isset($_POST['loginRadiusZipcode'])) {
                    $this->socialloginProfileData['Zipcode'] = trim($_POST['loginRadiusZipcode']);
                }
                // Province
                if (isset($_POST['loginRadiusProvince'])) {
                    $this->socialloginProfileData['Province'] = trim($_POST['loginRadiusProvince']);
                }
                // Email
                if (isset($email)) {
                    $this->socialloginProfileData['Email'] = $email;
                    $verify = true;
                } else {
                    $verify = false;
                }
                Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session
                if ($this->blockObj->getProfileFieldsRequired() == "1") {
                    $this->socialLoginAddNewUser($this->socialloginProfileData, $verify, false, '', true);
                } else {
                    $this->socialLoginAddNewUser($this->socialloginProfileData, $verify);
                }

            }
        } elseif (isset($_POST['LoginRadiusPopupCancel'])) { // popup cancelled
            Mage::getSingleton('core/session')->unsSocialLoginData(); // unset session

            Mage::getSingleton('core/session')->unsTmpPopupTxt();
            Mage::getSingleton('core/session')->unsTmpPopupMsg();
            Mage::getSingleton('core/session')->unsTmpShowForm();
            Mage::getSingleton('core/session')->unsTmpProfileData();
            Mage::getSingleton('core/session')->unsTmpEmailRequired();
            Mage::getSingleton('core/session')->unsTmpHideZipcode();

            header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
            die;
        }
        $this->SocialLoginShowLayout();
    }

    public function tokenHandle($token)
    {
        $loginhelper = Mage::helper('sociallogin/loginhelper');
        $loginRadiusSDK = Mage::helper('sociallogin/LoginRadiusSDK');
        // Fetch user profile using access token ......
        $responseFromLoginRadius = $loginRadiusSDK->fetchUserProfile($token);
        $userObj = json_decode($responseFromLoginRadius);

        if (isset($userObj->ID) && !empty($userObj->ID)) {
            // If linking variable is available then link account
            if ($this->blockObj->user_is_already_login()) {
                $this->loginRadiusSocialLinking(Mage::getSingleton("customer/session")->getCustomer()->getId(), $userObj->ID, $userObj->Provider, $userObj->ThumbnailImageUrl, true);
            }
            $this->socialloginProfileData = $loginhelper->socialLoginFilterData($userObj);

            //valid user, checking if user in sociallogin and customer entity tabel
            $queryResult = $loginhelper->loginRadiusRead("sociallogin", "get_user_by_social_id", array($userObj->ID), true);
            //Social Id Exist in Local DB
            if ($result = $queryResult->fetch()) {
                if ($result['verified'] == "0") {
                    $session = Mage::getSingleton('customer/session');
                    $session->addError(__('Please verify your email to login.'));
                    header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));
                    die;
                } else {
                    if ($this->blockObj->updateProfileData() != '1') {
                        $this->socialLoginUserLogin($result['entity_id'], $userObj->ID);
                    } else {
                        $this->socialloginProfileData['lrId'] = $userObj->ID;
                        $this->socialLoginAddNewUser($this->socialloginProfileData, false, true, $result['entity_id']);
                    }

                    return;
                }
            } //Social Id not Exist in Local DB and email not empty
            elseif (isset($userObj->Email[0]->Value) && !empty($userObj->Email[0]->Value)) {
                //if email is provided by provider then check if it's in table
                $email = $userObj->Email[0]->Value;
                $select = $loginhelper->loginRadiusRead("customer_entity", "email_already_exists", array($email), true);
                if ($rowArray = $select->fetch()) {
                    //user is in customer table
                    if ($this->blockObj->getLinking() == "1") { // Social Linking
                        $this->loginRadiusSocialLinking($rowArray['entity_id'], $userObj->ID, $userObj->Provider, $userObj->ThumbnailImageUrl);
                    }

                    if ($this->blockObj->updateProfileData() != '1') {
                        $this->socialLoginUserLogin($rowArray['entity_id'], $userObj->ID);
                    } else {
                        $this->socialloginProfileData = $loginhelper->socialLoginFilterData($userObj);
                        $this->socialloginProfileData['lrId'] = $userObj->ID;
                        $this->socialLoginAddNewUser($this->socialloginProfileData, false, true, $rowArray['entity_id']);
                    }
                } else {
                    $this->socialloginProfileData['lrId'] = $userObj->ID;
                    if ($this->blockObj->getProfileFieldsRequired() == 1) {
                        $loginhelper->setInSession($userObj->ID, $this->socialloginProfileData);
                        $loginhelper->setTmpSession($this->blockObj->getPopupText(), "", true, $this->socialloginProfileData, false);
                        // show a popup to fill required profile fields
                        $this->popupHandle();

                        return;
                    }
                    $this->socialLoginAddNewUser($this->socialloginProfileData);
                }
            } else {
                $emailRequired = true;
                if ($this->blockObj->getEmailRequired() == 0) { // dummy email
                    $email = $loginhelper->generateRandomEmail($userObj);
                    $this->socialloginProfileData['Email'] = $email;
                    $this->socialloginProfileData['lrId'] = $userObj->ID;
                    $emailRequired = false;
                }
                //
                $this->socialloginProfileData['lrToken'] = $this->loginRadiusAccessToken;
                //show required fields popup
                $loginhelper->setInSession($userObj->ID, $this->socialloginProfileData);
                if ($this->blockObj->getProfileFieldsRequired() == 1) {
                    // show a popup to fill required profile fields
                    $loginhelper->setTmpSession($this->loginRadiusPopMsg, "", true, $this->socialloginProfileData, $emailRequired);
                    $this->popupHandle();
                } elseif ($this->blockObj->getEmailRequired() == 1) {
                    $loginhelper->setTmpSession($this->loginRadiusPopMsg, "", true, array(), $emailRequired, true);
                    $this->popupHandle();
                } else {
                    //create new user without showing popup
                    $this->socialLoginAddNewUser($this->socialloginProfileData);
                }
            }
        } else {
            if ($this->blockObj->isDebuggingOn()) {
                Mage::getSingleton('core/session')->addNotice($userObj->description);
                session_write_close();
            }
            $refererUrl = $this->_getRefererUrl();
            if (empty($refererUrl)) {
                $refererUrl = Mage::getBaseUrl();
            }
            $this->getResponse()->setRedirect($refererUrl);

            return;
        }
    }

    function loginRadiusSocialLinking($entityId, $socialId, $provider, $thumbnail, $unique = false)
    {
        $session = Mage::getSingleton('customer/session');
        // check if any account from this provider is already linked
        if ($this->blockObj->user_is_already_login()) {
            if (Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "get_user_by_social_id", array($socialId))) {
                $session->addError(__('This accounts is already linked with an account.'));
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
                die;
            } elseif ($unique && Mage::helper('sociallogin/loginhelper')->loginRadiusRead("sociallogin", "provider exists in sociallogin", array($entityId, $provider))) {
                $session->addError(__('Multiple accounts cannot be linked from the same Social ID Provider.'));
                header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
                die;
            }
        }
        $socialLoginLinkData = array();
        $socialLoginLinkData['sociallogin_id'] = $socialId;
        $socialLoginLinkData['entity_id'] = $entityId;
        $socialLoginLinkData['provider'] = empty($provider) ? "" : $provider;
        $socialLoginLinkData['avatar'] = Mage::helper('sociallogin/loginHelper')->socialLoginFilterAvatar($socialId, $thumbnail, $provider);
        $socialLoginLinkData['avatar'] = ($socialLoginLinkData['avatar'] == "") ? null : $socialLoginLinkData['avatar'];
        Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", $socialLoginLinkData);
        if ($this->blockObj->user_is_already_login()) {
            $session->addSuccess(__('Account linked successfully.'));
            header("Location:" . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . "customer/account");
            die;
        }
    }

    function socialLoginUserLogin($entityId, $socialId, $loginOrRegister = 'Login')
    {
        $session = Mage::getSingleton("customer/session");
        $customer = Mage::getModel('customer/customer')->load($entityId);
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $session->setCustomerAsLoggedIn($customer);
        $functionForRedirectOption = 'get' . $loginOrRegister . 'RedirectOption';
        $Hover = $this->blockObj->$functionForRedirectOption();
        $functionForCustomRedirectOption = 'getCustom' . $loginOrRegister . 'RedirectOption';
        $write_url = $this->blockObj->$functionForCustomRedirectOption();
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        // check if logged in from callback page
        if (isset($_GET['loginradiuscheckout'])) {
            $currentUrl = Mage::helper('checkout/url')->getCheckoutUrl();
            Mage::app()->getResponse()->setRedirect($currentUrl)->sendResponse();
        }
        if ($Hover == 'account') {
            $currentUrl = $url . 'customer/account';
        } elseif ($Hover == 'index') {
            $currentUrl = $url;
        } elseif ($Hover == 'custom' && $write_url != '') {
            $currentUrl = $write_url;
        } elseif ($Hover == 'same') {
            $currentUrl = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : Mage::getUrl();
        } else {
            if (isset($_GET['redirect_to'])) {
                $currentUrl = trim($_GET['redirect_to']);
            } else {
                $currentUrl = $url;
            }

        }
        Mage::app()->getResponse()->setRedirect($currentUrl)->sendResponse();
    }

    // if token is posted then this function will be called. It will login user if already in database. else if email is provided by api, it will insert data and login user. It will handle all after token.

    function socialLoginAddNewUser($socialloginProfileData, $verify = false, $update = false, $customerId = '', $requiredFields = false)
    {
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        if (!$update) {
            $redirectionTo = 'Registration';
            // add new user magento way
            $customer = Mage::getModel("customer/customer");
        } else {
            $redirectionTo = 'Login';
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }
        $customer->website_id = $websiteId;
        $customer->setStore($store);
        if ($socialloginProfileData['FirstName'] != "") {
            $customer->firstname = $socialloginProfileData['FirstName'];
        }
        if (!$update) {
            $customer->lastname = $socialloginProfileData['LastName'] == "" ? $socialloginProfileData['FirstName'] : $socialloginProfileData['LastName'];
        } elseif ($update && $socialloginProfileData['LastName'] != "") {
            $customer->lastname = $socialloginProfileData['LastName'];
        }
        if (!$update) {
            $customer->email = $socialloginProfileData['Email'];
            $loginRadiusPwd = $customer->generatePassword(10);
            $customer->password_hash = md5($loginRadiusPwd);
        }
        if ($socialloginProfileData['BirthDate'] != "") {
            $customer->dob = $socialloginProfileData['BirthDate'];
        }
        if ($socialloginProfileData['Gender'] != "") {
            $customer->gender = $socialloginProfileData['Gender'];
        }
        $customer->setConfirmation(null);
        $customer->save();

        $address = Mage::getModel("customer/address");
        if (!$update) {
            $address->setCustomerId($customer->getId());
        }
        if (!$update) {
            $address->firstname = $customer->firstname;
            $address->lastname = $customer->lastname;
            $address->country_id = isset($socialloginProfileData['Country']) ? ucfirst($socialloginProfileData['Country']) : '';
            if (isset($socialloginProfileData['Zipcode'])) {
                $address->postcode = $socialloginProfileData['Zipcode'];
            }
            $address->city = isset($socialloginProfileData['City']) ? ucfirst($socialloginProfileData['City']) : '';
            // If country is USA, set up province
            if (isset($socialloginProfileData['Province'])) {
                $address->region = $socialloginProfileData['Province'];
            }
            $address->telephone = isset($socialloginProfileData['PhoneNumber']) ? ucfirst($socialloginProfileData['PhoneNumber']) : '';
            $address->company = isset($socialloginProfileData['Industry']) ? ucfirst($socialloginProfileData['Industry']) : '';
            $address->street = isset($socialloginProfileData['Address']) ? ucfirst($socialloginProfileData['Address']) : '';
            // set default billing, shipping address and save in address book
            $address->setIsDefaultShipping('1')->setIsDefaultBilling('1')->setSaveInAddressBook('1');
            if ($requiredFields) {
                $address->save();
            }
        }
        // add info in sociallogin table
        if (!$verify) {
            $fields = array();
            $fields['sociallogin_id'] = $socialloginProfileData['lrId'];
            $fields['entity_id'] = $customer->getId();
            $fields['avatar'] = $socialloginProfileData['thumbnail'];
            $fields['provider'] = $socialloginProfileData['Provider'];
            if (!$update) {
                Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", $fields);
            } else {
                Mage::helper('sociallogin/loginhelper')->SocialLoginInsert("sociallogin", array('avatar' => $socialloginProfileData['thumbnail']), true, array('entity_id = ?' => $customerId));
            }
            if (!$update) {
                $loginRadiusUsername = $socialloginProfileData['FirstName'] . " " . $socialloginProfileData['LastName'];
                // email notification to user
                if ($this->blockObj->notifyUser() == "1") {
                    $loginRadiusMessage = $this->blockObj->notifyUserText();
                    if ($loginRadiusMessage == "") {
                        $loginRadiusMessage = __("Welcome to ") . $store->getGroup()->getName() . ". " . __("You can login to the store using following e-mail address and password");
                    }
                    $loginRadiusMessage .= "<br/>" . "Email : " . $socialloginProfileData['Email'] . "<br/>" . __("Password") . " : " . $loginRadiusPwd;

                    Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("Welcome") . " " . $loginRadiusUsername . "!", $loginRadiusMessage, $socialloginProfileData['Email'], $loginRadiusUsername);
                }
                // new user notification to admin
                if ($this->blockObj->notifyAdmin() == "1") {
                    $loginRadiusAdminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                    $loginRadiusAdminName = Mage::getStoreConfig('trans_email/ident_general/name');
                    $loginRadiusMessage = trim($this->blockObj->notifyAdminText());
                    if ($loginRadiusMessage == "") {
                        $loginRadiusMessage = __("New customer has been registered to your store with following details");
                    }
                    $loginRadiusMessage .= "<br/>" . __("Name") . " : " . $loginRadiusUsername . "<br/>" . __("Email") . " : " . $socialloginProfileData['Email'];
                    Mage::helper('sociallogin/loginhelper')->loginRadiusEmail(__("New User Registration"), $loginRadiusMessage, $loginRadiusAdminEmail, $loginRadiusAdminName);
                }
            }
            //login and redirect user
            $this->socialLoginUserLogin($customer->getId(), $fields['sociallogin_id'], $redirectionTo);
        }
        if ($verify) {
            $loginRadiusUsername = $socialloginProfileData['FirstName'] . " " . $socialloginProfileData['LastName'];
            Mage::helper('sociallogin/loginhelper')->verifyUser($socialloginProfileData['lrId'], $customer->getId(), $socialloginProfileData['thumbnail'], $socialloginProfileData['Provider'], $socialloginProfileData['Email'], true, $loginRadiusUsername);
        }
    }

    public function popupHandle()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('Mage_Core_Block_Template', 'emailpopup', array('template' => 'sociallogin/popup.phtml'))
        );
        $this->renderLayout();

        return $this;
    }

    public function SocialLoginShowLayout()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action for AJAX
     */
    function ajaxAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _getSession()
    {
        return Mage::getSingleton('sociallogin/session');
    }
}