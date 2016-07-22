<?php
session_start();
include_once("src/Google_Client.php");
include_once("src/contrib/Google_Oauth2Service.php");
######### edit details ##########
$clientId = '167753457981-rhqi09o2nh2l2dcr67fr3o5fc4pbun3n.apps.googleusercontent.com'; //Google CLIENT ID
$clientSecret = 'chY90TnFav8n3PCKBoP0qks2'; //Google CLIENT SECRET
$redirectUrl = 'http://www.rekinza.com/backend/backend_home.php';  //return url (url to script)
$homeUrl = 'http://www.rekinza.com';  //return to home



##################################

$gClient = new Google_Client();
$gClient->setApplicationName('Google Login');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectUrl);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>