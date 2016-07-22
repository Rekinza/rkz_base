<?php

include '../../app/Mage.php';

Mage::app();

include '../db_config.php';

$region =$_POST['region'];

$regionModel = Mage::getModel('directory/region')->loadByCode($region,'IN');
$regionId = $regionModel->getId();

$allActivePaymentMethods = Mage::getModel('payment/config')->getActiveMethods();


echo $regionId;

?>