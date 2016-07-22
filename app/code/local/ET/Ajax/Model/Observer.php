<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Ajax_Model_Observer {
	
	/**
	 * Initialize product instance from request data
	 *
	 * @return Mage_Catalog_Model_Product || false
	 */
	protected function _initProduct($productId=0) {
	    if (!$productId) {
	        $productId = (int) Mage::app()->getFrontController()->getRequest()->getParam('product');
	    }
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
	
	public function handleOptions($observer){
		$event = $observer->getEvent();
		$controller = $observer->getControllerAction();
		$checkout_sesstion = Mage::getSingleton('checkout/session');
		$checkout_redirect = $checkout_sesstion->getCheckoutRedirect(true);
		
		if ($controller instanceof Mage_Core_Controller_Front_Action){
			$params = $controller->getRequest()->getParams();
			$product = $this->_initProduct(isset($params['id']) ? $params['id'] : 0);
			$is_grouped = $product && ($product->getTypeId() == 'grouped');
			$condition = ($checkout_redirect || isset($params['options']) || $is_grouped) && $controller->getRequest()->isAjax();
			if ( $condition ){
				$response = array();
				$response['error'] = 'ERROR';
				$layout = $controller->loadLayout()->getLayout();
				
				$_tpls = array(
					'product.info' => 'et/ajax/catalog/product/options.phtml'
				);
				$_blocks = array(
					'product.info' => false
				);
				
				if ( count($_tpls) ){
					foreach ($_tpls as $alias => $tpl){
						$block = $layout->getBlock($alias);
						if($block) $block->setTemplate($tpl);
					}
				}
				
				if ( count($_blocks) ){
					foreach ($_blocks as $alias => $flag){
						$block = $layout->getBlock($alias);
						if ($block){
							$_blocks[$alias] = $block->toHtml();
							if (!isset($response['success'])) {
								$response['success'] = 1;
								$response['options'] = $_blocks[$alias];
								unset($response['error']);
							}
						}
					}
				}
				
				$controller->getResponse()->setHeader('Content-Type', 'application/json');
 				$controller->getResponse()->setBody( Mage::helper('core')->jsonEncode( $response ) );
			}
		}
	}
}