<?php
/**
 * MageTitan's Skip Shipping Method
 * For assistance please contact devsupport@magetitan.com
 *
 * Copyright (c) 2011 NetMediaGroup
 * All rights reserved.
 *
 * Redistribution with or without modification, without permission,
 * is strictly prohibited.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER 
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT 
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN 
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    MageTitan
 * @copyright   Copyright (c) 2011 NetMediaGroup  (www.netmediagroup.com)
 * @license     http://www.magetitan.com/licenses/restricted.html
 * @package     MageTitan_SkipShippingMethod
 */

require_once 'Mage/Checkout/controllers/OnepageController.php';
class MageTitan_SkipShippingMethod_Checkout_OnepageController extends Mage_Checkout_OnepageController
{

  public function saveBillingAction()
  {
    parent::saveBillingAction();
    if ($this->getRequest()->isPost()) { $this->checkAndSetShippingMethod(); }
  }

  public function saveShippingAction()
  {
    parent::saveShippingAction();
    if ($this->getRequest()->isPost()) { $this->checkAndSetShippingMethod(); }
  }

  protected function checkAndSetShippingMethod()
  {
    if ( Mage::helper('skipshippingmethod')->isEnabled() ) {
      $result = Mage::helper('core')->jsonDecode($this->getResponse()->getBody());
      if (!isset($result['error']) && isset($result['goto_section']) && $result['goto_section'] == 'shipping_method') {
        $code = $this->_getShippingCode();

        if ( is_null($code) ) {
          $result = array(
            'error' => 1,
            'message' => array(Mage::getStoreConfig('checkout/skipshippingmethod/error'))
          );
        } else {
          $result = $this->getOnepage()->saveShippingMethod($code);
          if( !$result ) { 
            Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getOnepage()->getQuote()));
            $this->getOnepage()->getQuote()->getShippingAddress()->collectTotals()->save();
            $result['goto_section'] = 'payment';
            $result['allow_sections'] = array('shipping');
            $result['update_section'] = array(
              'name' => 'payment-method',
              'html' => $this->_getPaymentMethodsHtml()
              );
          }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
      }
    }
  }

  protected function _getPaymentMethodsHtml()
  {
    $layout = $this->getLayout();
    $update = $layout->getUpdate();
    $update->setCacheId('LAYOUT_'.Mage::app()->getStore()->getId().md5('checkout_onepage_paymentmethod'));
    $update->load('checkout_onepage_paymentmethod');
    $layout->generateXml();
    $layout->generateBlocks();
    $output = $layout->getOutput();
    return $output;
  }

  protected function _getShippingCode()
  {
    switch ( Mage::getStoreConfig('checkout/skipshippingmethod/method') ) {
      case MageTitan_SkipShippingMethod_Model_Adminhtml_System_Method::FIRST_AVAILABLE:
        $code = $this->_getFirstShippingMethod();
        break;
      case MageTitan_SkipShippingMethod_Model_Adminhtml_System_Method::LOWEST_COST:
        $code = $this->_getLowestShippingRate();
        break;
      case MageTitan_SkipShippingMethod_Model_Adminhtml_System_Method::HIGHEST_COST:
        $code = $this->_getHighestShippingRate();
        break;
    }
    return $code;

  }

  protected function _getFirstShippingMethod()
  {
    $code = null;
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        $code = $rate->getCode();break;
      }
      break;
    }
    return $code;
  }

  protected function _getLowestShippingRate()
  {
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    $method = array('code' => null, 'value' => -1);
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        if ( is_null($method['code']) || $rate->getPrice() < $method['price'] ) {
          $method['code'] = $rate->getCode();
          $method['price'] = $rate->getPrice();
        }
        if ( $method['price'] == 0 ) { break; }
      }
      if ( $method['price'] == 0 ) { break; }
    }
    return $method['code'];
  }

  protected function _getHighestShippingRate()
  {
    $groupedRates = $this->getOnepage()->getQuote()->getShippingAddress()->getGroupedAllShippingRates();
    $method = array('code' => null, 'value' => -1);
    foreach ( $groupedRates as $rates ) { 
      foreach ( $rates as $rate ) {
        if ( is_null($method['code']) || $rate->getPrice() > $method['price'] ) {
          $method['code'] = $rate->getCode();
          $method['price'] = $rate->getPrice();
        }
      }
    }
    return $method['code'];
  }

}