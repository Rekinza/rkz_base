<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Ajax_WishlistController /*extends Mage_Wishlist_IndexController*/ extends Mage_Core_Controller_Front_Action {

	private function _jsonEncode($response, $type){
		if ( isset($response['error']) ){
// 			$response['actions'] = $this->getLayout()->createBlock(
// 					'core/template',
// 					'actions_compare_error',
// 					array('template' => "et/ajax/wishlist/actions_{$type}_error.phtml")
// 			)->toHtml();
		} else {
// 			$response['actions'] = $this->getLayout()->createBlock(
// 					'core/template',
// 					'actions_compare_success',
// 					array('template' => 'et/ajax/wishlist/actions_{$type}_success.phtml')
// 			)->toHtml();
		}
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return $this;
	}
	
	protected function _getWishlist($wishlistId = null) {
		$wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            /* @var Mage_Wishlist_Model_Wishlist $wishlist */
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }

            if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
                $wishlist = null;
                Mage::throwException(
                    Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
                );
            }

            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
            return false;
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e,
                Mage::helper('wishlist')->__('Wishlist could not be created.')
            );
            return false;
        }

        return $wishlist;
	}
	
	public function addAction() {
	
		$response = array();
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
			$response['error'] = $this->__('Wishlist Has Been Disabled By Admin');
		}
		
		if (!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['error'] = $this->__('Please Login First');
		}
	
		if (empty($response)) {
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();
			if ( !$wishlist ) {
				$response['error'] = $this->__('Unable to Create Wishlist');
			} else {
				$productId = (int)$this->getRequest()->getParam('product');
				
				if (!$productId) {
					$response['error'] = $this->__('Product Not Found');
				} else {
	
					$product = Mage::getModel('catalog/product')->load($productId);
					
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['error'] = $this->__('Invalid product [' . $productId . '].');
					} else {
	
						try {
							$requestParams = $this->getRequest()->getParams();
				            if ($session->getBeforeWishlistRequest()) {
				                $requestParams = $session->getBeforeWishlistRequest();
				                $session->unsBeforeWishlistRequest();
				            }
				            $buyRequest = new Varien_Object($requestParams);
	
							$result = $wishlist->addNewItem($product, $buyRequest);
				            if (is_string($result)) {
				                Mage::throwException($result);
				            }
				            $wishlist->save();
	
							Mage::dispatchEvent(
				                'wishlist_add_product',
				                array(
				                    'wishlist' => $wishlist,
				                    'product' => $product,
				                    'item' => $result
				                )
				            );
	
							Mage::helper('wishlist')->calculate();
							
							$referer = $session->getBeforeWishlistUrl();
							if ($referer) {
								$session->setBeforeWishlistUrl(null);
							} else {
								$referer = $this->_getRefererUrl();
							}
							
							/**
							 *  Set referer to avoid referring to the compare popup window
							 */
							$session->setAddActionReferer($referer);
							
							$response['success'] = 1;
							$response['message'] = $this->__('<strong>%1$s</strong> has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping.', $product->getName(), Mage::helper('core')->escapeUrl($referer));
	
							Mage::unregister('wishlist');
	
							$this->loadLayout();
							$wishlist_sidebar = $this->getRequest()->getParam('wishlist_sidebar');
                            if (isset($wishlist_sidebar) && (int)$wishlist_sidebar) {
                                $updates = array('top.links', 'wishlist_sidebar');
                            } else {
                                $updates = array('top.links');
                            }
                            Mage::helper('ajax')->addUpdatesToResponse($response, $updates);
						} catch (Mage_Core_Exception $e) {
							$response['error'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						} catch (Exception $e) {
							$response['error'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}
	
		}
		return $this->_jsonEncode($response, 'add');
	}
	
	public function removeAction() {
		
		$response = array();
		if ( !Mage::getStoreConfigFlag('wishlist/general/active') ) {
			$response['error'] = $this->__('Wishlist Has Been Disabled By Admin');
		}
		
		if ( !Mage::getSingleton('customer/session')->isLoggedIn() ){
			$response['error'] = $this->__('Please Login First');
		}
		
		$id = (int) $this->getRequest()->getParam('item');
		$item = Mage::getModel('wishlist/item')->load($id);
		
		if ( !$item->getId() ) {
			$response['error'] = $this->__('Item Not Found ['.$id.']');
		} else {
			
			
			try {
				$wishlist = $this->_getWishlist($item->getWishlistId());
				if ( !$wishlist ) {
					$response['error'] = $this->__('Unable to find your wishlist');
				} else {
					$response['item'] = $item->getData();
					$item->delete();
					$wishlist->save();
					Mage::helper('wishlist')->calculate();
					$response['success'] = 1;
					$response['message'] = $this->__('Item has been removed from your wishlist.');
				}
			} catch (Mage_Core_Exception $e) {
				$response['error'] =  $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage());
			} catch (Exception $e) {
				$response['error'] =  $this->__('An error occurred while deleting the item from wishlist:: %s', $e->getMessage());
			}
		}
	
		return $this->_jsonEncode($response, 'add');
	}
	
}