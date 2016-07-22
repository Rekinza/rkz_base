<?php

// Define LoginRadius domain
define('LR_DOMAIN', 'api.loginradius.com');

class Loginradius_Sociallogin_Helper_LoginRadiusSDK extends Mage_Core_Helper_Abstract
{
  /**
   * LoginRadius function - It validate against GUID format of keys.
   *
   * @param string $key data to validate.
   *
   * @return boolean. If valid - true, else - false.
   */
  public function loginradiusValidateKey($key)
  {
    if (empty($key) || !preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/i', $key)) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * LoginRadius function - To fetch social profile data from the user's social account after authentication. The social profile will be retrieved via oAuth and OpenID protocols. The data is normalized into LoginRadius' standard data format.
   *
   * @param string  $accessToken LoginRadius access token
   * @param boolean $raw         If true, raw data is fetched
   *
   * @return object User profile data.
   */
  public function fetchUserProfile($accessToken, $raw = false)
  {
    $ValidateUrl = 'https://' . LR_DOMAIN . '/api/v2/userprofile?access_token=' . $accessToken;
    if ($raw) {
      $ValidateUrl = 'https://' . LR_DOMAIN . '/api/v2/userprofile/raw?access_token=' . $accessToken;
      return $this->accessLoginradiusApi($ValidateUrl);
    }
    return $this->accessLoginradiusApi($ValidateUrl);
  }

  public function accessLoginradiusApi($url, $data = array(), $isPost = false)
  {

    if (Mage::helper('sociallogin')->isCurlEnabled()) {
      $JsonResponse = $this->curlApiMethod($url, $data, $isPost );
    } else {
      $JsonResponse = $this->fsockopenApiMethod($url, $data, $isPost);
    }
    $result = json_decode($JsonResponse);
    if (is_object($result) && isset($result->customError)) {
      $method = 'GET';

      $client = new Varien_Http_Client($url);
      $response = $client->request($method);
      $Response = $response->getBody();
      return $Response;

    }
    return $JsonResponse;

  }

  public function curlApiMethod($url, $data, $post = false)
  {
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 50);
    curl_setopt($curl_handle, CURLOPT_TIMEOUT, 50);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    if ($post) {
      curl_setopt($curlHandle, CURLOPT_POST, 1);
      curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
      curl_setopt($curlHandle, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or !ini_get('safe_mode'))) {
      curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    } else {
      curl_setopt($curl_handle, CURLOPT_HEADER, 1);
      $url = curl_getinfo($curl_handle, CURLINFO_EFFECTIVE_URL);
      curl_close($curl_handle);
      $curl_handle = curl_init();
      $url = str_replace('?', '/?', $url);
      curl_setopt($curl_handle, CURLOPT_URL, $url);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    }
    $JsonResponse = curl_exec($curl_handle);
    $httpCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
    if (in_array($httpCode, array(400, 401, 403, 404, 500, 503))) {
      $JsonResponse = json_encode(array("customError" => true, "Messages" => "Error in using curl connection ,Service connection error occurred"));
    } else {
      if (curl_errno($curl_handle) == 28) {
        $JsonResponse = json_encode(array("customError" => true, "Messages" => "Connection timeout"));
      }
    }
    curl_close($curl_handle);
    return $JsonResponse;
  }

  public function fsockopenApiMethod($ValidateUrl, $data, $post = false)
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
      $jsonResponse = json_encode(array("customError" => true, "Messages" => "Error in using FSOCKOPEN connection method, Service connection timeout occurred"));
    }
    if (!$jsonResponse) {
      $jsonResponse = json_encode(array("customError" => true, "Messages" => "FSOCKOPEN not working"));
    }
    return $jsonResponse;
  }

}