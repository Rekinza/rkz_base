<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require 'Mage/Checkout/controllers/CartController.php';
class ET_Ajax_Checkout_CartController extends Mage_Checkout_CartController {

    /**
	 * Initialize product instance from request data
	 *
	 * @return Mage_Catalog_Model_Product || false
	 */
	protected function _initProduct() {
		$productId = (int) $this->getRequest()->getParam('product');
		if ($productId) {
			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getId())
				->load($productId);
			if ($product->getId()) {
				return $product;
			}
		}
		return false;
	}
	
	public function addAction() {
	    
	    if (!$this->getRequest()->isAjax()){
	        return parent::addAction();
	    }
	    
	    if (!$this->_validateFormKey()) {
	        //$this->_goBack();
	        //return;
	        $response['error'] = $this->__('Invalid formkey');
	    }
	    $cart   = $this->_getCart();
	    $params = $this->getRequest()->getParams();
	    $response = array();
	    try {
	        if (isset($params['qty'])) {
	            $filter = new Zend_Filter_LocalizedToNormalized(
	                array('locale' => Mage::app()->getLocale()->getLocaleCode())
	            );
	            $params['qty'] = $filter->filter($params['qty']);
	        }
	
	        $product = $this->_initProduct();
	        $related = $this->getRequest()->getParam('related_product');
	
	        /**
	         * Check product availability
	        */
	        if (!$product) {
	            // $this->_goBack();
	            // return;
	            $response['error'] = $this->__('Invalid product ['.(int)$params['product'].']');
	        }
	
	        $cart->addProduct($product, $params);
	        if (!empty($related)) {
	            $cart->addProductsByIds(explode(',', $related));
	        }
	
	        $cart->save();
	
	        $this->_getSession()->setCartWasUpdated(true);
	
	        /**
	         * @todo remove wishlist observer processAddToCart
	        */
	        Mage::dispatchEvent('checkout_cart_add_product_complete',
	            array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
	        );
	
	        if (!$this->_getSession()->getNoCartRedirect(true)) {
	            if (!$cart->getQuote()->getHasError()) {
	                $this->loadLayout();
	                $response['success'] = 1;
	                $response['actions'] = $this->getLayout()->getBlock('after_add_success_actions')->toHtml();
	                $response['message'] = $this->__('<strong>%s</strong> has been added to your bag.', Mage::helper('core')->escapeHtml($product->getName()));
	                Mage::helper('ajax')->addUpdatesToResponse($response, array('ajaxcart', 'top.links'));
	            } else {
	                $quote_errors = $cart->getQuote()->getErrors();
	                $response['error'] = '';
	                foreach ($quote_errors as $err) {
	                    $response['error'] .= $err->getText();
	                }
	                // Mage_Core_Model_Message_Error
	            }
	            // $this->_goBack();
	        } else {
	            $response['error'] = 'getNoCartRedirect = true';
	        }
	    } catch (Mage_Core_Exception $e) {
	       
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $response['error'] = '';
				$messages = array_unique(explode("\n", $e->getMessage()));
                
				foreach ($messages as $message) {
					
					$response['error']=$message;
                }
            }
            
           
	    } catch (Exception $e) {
	        // $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
	        // Mage::logException($e);
	        // $this->_goBack();
	        $this->loadLayout();
            $response['error']   = Mage::helper('core')->escapeHtml($e->getMessage());
            $response['actions'] = $this->getLayout()->getBlock('after_add_error_actions')->toHtml();
	    }
	    $this->getResponse()->setHeader('Content-type', 'application/json');
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	
	public function deleteAction() {
	    
	    if (!$this->getRequest()->isAjax()){
	        return parent::deleteAction();
	    }
		
        $id = (int) $this->getRequest()->getParam('id');
        $response = array();
        if ($id) {
            try {
                Mage::getSingleton('checkout/cart')->removeItem($id)->save();

                $this->loadLayout();
                $response['success'] = 1;
                $response['message'] = $this->__('Item was removed successfully.');
                $in_cart = $this->getRequest()->getParam('in_cart');
                if (isset($in_cart) && (int)$in_cart) {
                    $updates = array('ajaxcart', 'top.links', 'checkout_cart');
                } else {
                    $updates = array('ajaxcart', 'top.links');
                }
                Mage::helper('ajax')->addUpdatesToResponse($response, $updates);
            } catch (Exception $e) {
                $response['error'] = $this->__('Can not remove the item.');
            }
        }

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	
	public function updateItemOptionsAction(){
	    
	    if (!$this->getRequest()->isAjax()){
	        return parent::updateItemOptionsAction();
	    }
	    
	    $cart   = $this->_getCart();
	    $id = (int) $this->getRequest()->getParam('id');
	    $params = $this->getRequest()->getParams();
	    $response = array();
	    if (!isset($params['options'])) {
	        $params['options'] = array();
	    }
	    try {
	        if (isset($params['qty'])) {
	            $filter = new Zend_Filter_LocalizedToNormalized(
	                array('locale' => Mage::app()->getLocale()->getLocaleCode())
	            );
	            $params['qty'] = $filter->filter($params['qty']);
	        }
	    
	        $quoteItem = $cart->getQuote()->getItemById($id);
	        if (!$quoteItem) {
	            Mage::throwException($this->__('Quote item is not found.'));
	        }
	    
	        $item = $cart->updateItem($id, new Varien_Object($params));
	        if (is_string($item)) {
	            Mage::throwException($item);
	        }
	        if ($item->getHasError()) {
	            Mage::throwException($item->getMessage());
	        }
	    
	        $related = $this->getRequest()->getParam('related_product');
	        if (!empty($related)) {
	            $cart->addProductsByIds(explode(',', $related));
	        }
	    
	        $cart->save();
	    
	        $this->_getSession()->setCartWasUpdated(true);
	    
	        Mage::dispatchEvent('checkout_cart_update_item_complete',
	            array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
	        );
	        if (!$this->_getSession()->getNoCartRedirect(true)) {
	            if (!$cart->getQuote()->getHasError()) {
	                $this->loadLayout();
	                $response['success'] = 1;
	                $response['actions'] = $this->getLayout()->getBlock('after_update_success_actions')->toHtml();
	                $response['message'] = $this->__('<strong>%s</strong> was updated in your shopping cart.', Mage::helper('core')->escapeHtml($item->getProduct()->getName()));
	                Mage::helper('ajax')->addUpdatesToResponse($response, array('ajaxcart', 'top.links'));
	            }
	        }
	    } catch (Mage_Core_Exception $e) {
	        $response['error'] = $e->getMessage();
	    } catch (Exception $e) {
	        $response['error'] = $this->__('Cannot update the item.');
	    }
	    
	    $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	
	
}