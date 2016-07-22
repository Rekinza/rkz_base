<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function configdataAction() {
	    $this->loadLayout();
		$this->renderLayout();
	}
	
	public function configAction(){
	    if ($this->getRequest()->isPost()&&$this->getRequest()->isAjax()){
	        $path = $this->getRequest()->getParam('path');
	        $value = $this->getRequest()->getParam('value');
	        if ($path){
	            $scope = 'stores';
    			$scope_id = Mage::app()->getStore()->getStoreId();
    			Mage::getConfig()->saveConfig($path, $value, $scope, $scope_id);
	        }
	        die('1');
	    }
	}
	public function noconfigAction(){
	    if ($this->getRequest()->isPost()&&$this->getRequest()->isAjax()){
	        $path = $this->getRequest()->getParam('path');
	        if ($path){
	            $scope = 'stores';
	            $scope_id = Mage::app()->getStore()->getStoreId();
	            $rs = Mage::getConfig()->deleteConfig($path, $scope, $scope_id) && Mage::getConfig()->deleteConfig($path);;
	            echo $rs ? 1 : 0;
	            die;
	        }
	        die('0');
	    }
	}
}