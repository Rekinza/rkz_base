<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function UnAction(){
		
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		

		$query = "DELETE FROM core_resource WHERE code = 'slider_setup';";
		 
		$writeConnection->query($query);
		
		 $this->getResponse()->setRedirect( Mage::getBaseUrl() );
	}
}