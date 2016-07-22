<?php

include '../../app/Mage.php';

Mage::app();

$order_number =$_POST['order_number'];

$order = new Mage_Sales_Model_Order();
$order->loadByIncrementId($order_number);

$payment_method = $order->getPayment()->getMethodInstance()->getTitle();

$option = '';
$refund_modes = array('Kinzacash','Bank Transfer','Donate to Charity','Refund to Debit Card/Credit Card/Netbanking');	

if ($payment_method != 'Debit Card/Credit Card/Netbanking')
{
	$looptill = 3;
}
else
{
	$looptill = 4;
}
for ($j =0;$j<$looptill;$j++)
{	
  $option .= '<option value = "'.$refund_modes[$j].'">'.$refund_modes[$j].'</option>';
}

echo $option;


?>