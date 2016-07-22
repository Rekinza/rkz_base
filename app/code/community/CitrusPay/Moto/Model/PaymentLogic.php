<?php



class CitrusPay_Moto_Model_PaymentLogic extends Mage_Payment_Model_Method_Abstract
{
	protected $_isGateway = true;
	protected $_canAuthorize = true;

	protected $_code = "moto";


	/**
	 * Order instance
	 */
	protected $_order;
	protected $_config;
	protected $_payment;
	protected $_redirectUrl;

	/**
	 * @return Mage_Checkout_Model_Session
	 */
	protected function _getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}

	/**
	 * Return order instance loaded by increment id'
	 *
	 * @return Mage_Sales_Model_Order
	 */
	protected function _getOrder()
	{
		return $this->_order;
	}


	public function authorize(Varien_Object $payment, $amount)
	{
		Mage::getSingleton('customer/session')->setAmount($amount);
		return $this;
	}

	
	public function getOrderPlaceRedirectUrl()
	{
		return Mage::getUrl('moto/index/index', array('_secure' => true));
	}
	
}