<?php
/**
 * @package ET_Ajax
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Ajax_IndexController extends Mage_Core_Controller_Front_Action {
	
	/**
	 * Reset Module
	 * Remove setup infomation from core_resource
	 * Remove configuration values from core_config_data
	 *
	 * Usage: /index.php/oxynic/reset
	 */
	
	public function productAction(){
		$productId = (int) $this->getRequest()->getParam('id');
	
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');
	
		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);
	
		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
// 			$this->loadLayout();
// 			$blockcontent = $this->getLayout()->getBlock('product.info')->toHtml();
// 			// $this->getRequest()->setBody($blockcontent);
// 			die($blockcontent);

		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}
	
}