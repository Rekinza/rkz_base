<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Catalog/controllers/Product/CompareController.php';
class ET_Ajax_Catalog_Product_CompareController extends Mage_Catalog_Product_CompareController {

    private function jsonResponse($response, $type){
        $this->loadLayout();
    	if (isset($response['error'])) {
    		$response['actions'] = $this->getLayout()->createBlock(
    				'core/template',
    				'actions_compare_error',
    				array('template' => "et/ajax/compare/actions_{$type}_error.phtml")
    		)->toHtml();
    	} else {
    		$response['actions'] = $this->getLayout()->createBlock(
    				'core/template',
    				'actions_compare_success',
    				array('template' => "et/ajax/compare/actions_{$type}_success.phtml")
    		)->toHtml();
    	}
    	$this->getResponse()->setHeader('Content-type', 'application/json');
    	$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    	return $this;
    }

    /**
     * Add item to compare list
     */
    public function addAction() {
    	if (! $this->getRequest()->isAjax()) {
    		return parent::addAction();
    	}
    	
    	// init response
    	$response = array();
    	
    	if (!$this->_validateFormKey()) {
    		$response['error'] = $this->__('Invalid form key');
    		return $this->jsonResponse($response, 'add');
    	}

    	$productId = (int)$this->getRequest()->getParam('product');
// 		$items     = Mage::helper('catalog/product_compare')->getItemCollection();
// 		foreach ($items as $p){
// 			if ( $productId == $p->getId() ){
// 				// already added
// 				$response['error'] = $this->__('The product %s is already in comparison list.', Mage::helper('core')->escapeHtml($p->getName()));
// 				return $this->jsonResponse($response, 'add');
// 			}
// 		}
		
        if ($productId && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn()) ) {
        	try {
        		// parameters
        		$compare_sidebar = $this->getRequest()->getParam('compare_sidebar', 0);
        		
        		$product = Mage::getModel('catalog/product')
                	->setStoreId(Mage::app()->getStore()->getId())
                	->load($productId);

	            if ($product->getId()/* && !$product->isSuper()*/) {
	                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
	                Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
	                
	                Mage::helper('catalog/product_compare')->calculate(true);
	                $response['item_count'] = Mage::helper('catalog/product_compare')->getItemCount();
	                $response['success'] = 1;
	                $response['message'] = $this->__('The product <strong>%s</strong> has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
	                
	                // render blocks
	                $this->loadLayout();
	                if ($compare_sidebar) {
	                	$response['compare_sidebar'] = $this->getLayout()->getBlock('ajax_compare_sidebar')->toHtml();
	                }
	            } else {
	            	$response['error'] = $this->__('Invalid product ['.(int)$productId.']');
	            }
        	} catch (Exception $e) {
        		$response['error']   = Mage::helper('core')->escapeHtml($e->getMessage());
        	}
            
        }
        return $this->jsonResponse($response, 'add');
    }

    /**
     * Remove item from compare list
     */
    public function removeAction() {
        if (! $this->getRequest()->isAjax()) {
    		return parent::removeAction();
    	}
    	 
    	// init response
    	$response = array();
    	 
    	$productId = (int)$this->getRequest()->getParam('product');
    	$helper    = Mage::helper('catalog/product_compare');
    	
    	if ($productId) {
    		try {
    			// parameters
    			$compare_sidebar = $this->getRequest()->getParam('compare_sidebar', 0);
    	
    			$product = Mage::getModel('catalog/product')
    				->setStoreId(Mage::app()->getStore()->getId())
    				->load($productId);
    	
    			if ($product->getId()/* && !$product->isSuper()*/) {
    				/* @var $item Mage_Catalog_Model_Product_Compare_Item */
	                $item = Mage::getModel('catalog/product_compare_item');
	                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
	                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
	                } elseif ($this->_customerId) {
	                    $item->addCustomerData(
	                        Mage::getModel('customer/customer')->load($this->_customerId)
	                    );
	                } else {
	                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
	                }
	
	                $item->loadByProduct($product);
                	
	                if($item->getId()) {
	                	$response['success'] = $item->delete();
	                	$response['message'] = $this->__('The product <strong>%s</strong> has been removed from comparison list.', $product->getName());
	                	Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
	                	// Mage::helper('catalog/product_compare')->calculate();
	                	$response['item_count'] = Mage::helper('catalog/product_compare')->getItemCount();
	                }
    				 
    				// render blocks
    				$this->loadLayout();
    				if ($compare_sidebar) {
    				    // Mage::log( array_keys($this->getLayout()->getAllBlocks()) );
    					$response['compare_sidebar'] = $this->getLayout()->getBlock('ajax_compare_sidebar')->toHtml();
    				}
    			} else {
    				$response['error'] = $this->__('Invalid product ['.(int)$productId.']');
    			}
    		} catch (Exception $e) {
    			$response['error']   = Mage::helper('core')->escapeHtml($e->getMessage());
    		}
    	
    	}
    	return $this->jsonResponse($response, 'remove');
    }

    /**
     * Remove all items from comparison list
     */
    public function clearAction() {
    	
        if (! $this->getRequest()->isAjax()) {
    		return parent::clearAction();
    	}
    	 
    	// init response
    	$response = array();
    	 
    	try {
    		// parameters
    		$compare_sidebar = $this->getRequest()->getParam('compare_sidebar', 0);
    	
    		$items = Mage::getResourceModel('catalog/product_compare_item_collection');

		    if (Mage::getSingleton('customer/session')->isLoggedIn()) {
		        $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
		    } elseif ($this->_customerId) {
		        $items->setCustomerId($this->_customerId);
		    } else {
		        $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
		    }
    			
		    $items->clear();
		    $response['success'] = 1;
    		$response['message'] = $this->__('The comparison list was cleared.');
		    // Mage::helper('catalog/product_compare')->calculate();
		    $response['item_count'] = Mage::helper('catalog/product_compare')->getItemCount();
    			
			// render blocks
		    $this->loadLayout();
    		if ($compare_sidebar) {
    			$response['compare_sidebar'] = $this->getLayout()->getBlock('ajax_compare_sidebar')->toHtml();
    		}
    	} catch (Exception $e) {
    		$response['error'] = Mage::helper('core')->escapeHtml($e->getMessage());
    	}
    	
    	return $this->jsonResponse($response, 'clear');
    }

}
