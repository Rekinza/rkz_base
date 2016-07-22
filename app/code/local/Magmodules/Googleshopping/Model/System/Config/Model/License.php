<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Googleshopping
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Googleshopping_Model_System_Config_Model_License extends Mage_Core_Model_Config_Data {

    public function afterLoad() {
       $data = call_user_func(str_rot13('onfr64_qrpbqr'), "JG1vZHVsZSA9ICdNYWdtb2R1bGVzX0dvb2dsZXNob3BwaW5nJzsgJG1vZHVsZV92ZXJzaW9uID0gJ01hZ21vZHVsZXNfR1Nob3Bvb2dsZSM3MTkyJzsgJG1vZHVsZV9wYXRoID0gJ2dvb2dsZXNob3BwaW5nL2dlbmVyYWwvJzsgJG1vZHVsZV9zZXJ2ZXIgPSBzdHJfcmVwbGFjZSgnd3d3LicsICcnLCAkX1NFUlZFUlsnSFRUUF9IT1NUJ10pOyAkbW9kdWxlX2luc3RhbGxlZCA9IE1hZ2U6OmdldENvbmZpZygpLT5nZXROb2RlKCktPm1vZHVsZXMtPk1hZ21vZHVsZXNfR29vZ2xlc2hvcHBpbmctPnZlcnNpb247IHJldHVybiBiYXNlNjRfZW5jb2RlKGJhc2U2NF9lbmNvZGUoYmFzZTY0X2VuY29kZSgkbW9kdWxlIC4gJzsnIC4gJG1vZHVsZV92ZXJzaW9uIC4gJzsnIC4gJG1vZHVsZV9pbnN0YWxsZWQgLiAnOycgLiB0cmltKE1hZ2U6OmdldE1vZGVsKCdjb3JlL2NvbmZpZ19kYXRhJyktPmxvYWQoJG1vZHVsZV9wYXRoIC4gJ2xpY2Vuc2Vfa2V5JywgJ3BhdGgnKS0+Z2V0VmFsdWUoKSkgLiAnOycgLiAkbW9kdWxlX3NlcnZlciAuICc7JyAuIE1hZ2U6OmdldFVybCgpIC4gJzsnIC4gTWFnZTo6Z2V0U2luZ2xldG9uKCdhZG1pbi9zZXNzaW9uJyktPmdldFVzZXIoKS0+Z2V0RW1haWwoKSAuICc7JyAuIE1hZ2U6OmdldFNpbmdsZXRvbignYWRtaW4vc2Vzc2lvbicpLT5nZXRVc2VyKCktPmdldE5hbWUoKSAuICc7JyAuICRfU0VSVkVSWydTRVJWRVJfQUREUiddKSkpOw==");
	   $this->setValue(eval($data));
    }

    static function isEnabled() {
		return eval(call_user_func(str_rot13('onfr64_qrpbqr'), "JG1vZHVsZV92ZXJzaW9uID0gJ01hZ21vZHVsZXNfR1Nob3Bvb2dsZSM3MTkyJzsgJG1vZHVsZV9wYXRoID0gJ2dvb2dsZXNob3BwaW5nL2dlbmVyYWwvJzsgJG1vZHVsZV9zZXJ2ZXIgPSBzdHJfcmVwbGFjZSgnd3d3LicsICcnLCAkX1NFUlZFUlsnSFRUUF9IT1NUJ10pOyAka2V5ID0gdHJpbShNYWdlOjpnZXRNb2RlbCgnY29yZS9jb25maWdfZGF0YScpLT5sb2FkKCRtb2R1bGVfcGF0aCAuICdsaWNlbnNlX2tleScsICdwYXRoJyktPmdldFZhbHVlKCkpOyAkZ2VuX2tleSA9IHNoYTEoc2hhMSgkbW9kdWxlX3ZlcnNpb24gLiAnX21hZ18nIC4gJG1vZHVsZV9zZXJ2ZXIpKTsgJHN0cmluZyA9IGV4cGxvZGUoJy0nLCAka2V5KTsgaWYoJHN0cmluZ1swXSA9PSAnd2NkJykgeyAka2V5ID0gJHN0cmluZ1sxXTsgJG1vZHVsZV9zZXJ2ZXIgPSBhcnJheV9yZXZlcnNlKGV4cGxvZGUoJy4nLCAkbW9kdWxlX3NlcnZlcikpOyAkbW9kdWxlX3NlcnZlciA9ICRtb2R1bGVfc2VydmVyWzFdIC4gJy4nIC4gJG1vZHVsZV9zZXJ2ZXJbMF07ICRnZW5fa2V5ID0gc2hhMShzaGExKCRtb2R1bGVfdmVyc2lvbiAuICdfbWFnXycgLiAkbW9kdWxlX3NlcnZlcikpOyB9IGlmKCRnZW5fa2V5ICE9ICRrZXkpIHsgTWFnZTo6Z2V0Q29uZmlnKCktPnNhdmVDb25maWcoJG1vZHVsZV9wYXRoIC4gJ2VuYWJsZWQnLCAwKTsgTWFnZTo6Z2V0Q29uZmlnKCktPmNsZWFuQ2FjaGUoKTsgTWFnZTo6Z2V0U2luZ2xldG9uKCdhZG1pbmh0bWwvc2Vzc2lvbicpLT5hZGRFcnJvcihNYWdlOjpoZWxwZXIoJ2dvb2dsZXNob3BwaW5nJyktPl9fKCJUaGUgTWFnbW9kdWxlcyBHb29nbGUgU2hvcHBpbmcgZXh0ZW5zaW9uIGNvdWxkbid0IGJlIGVuYWJsZWQuIFBsZWFzZSBtYWtlIHN1cmUgeW91IGFyZSB1c2luZyBhIHZhbGlkIGxpY2Vuc2Uga2V5LiIpKTsgcmV0dXJuIGZhbHNlOyB9IGVsc2UgeyByZXR1cm4gdHJ1ZTsgfQ=="));
    }
        
}