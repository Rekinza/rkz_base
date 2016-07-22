<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
    }
	
	public function ajaxGetParentsAction(){
		$response = $this->getResponse();
		if ($params = Mage::app()->getRequest()->getParams()) {
			$output = (object)array( 'success' => true, 'items' => array() );
			
			if( $params['position_id'] ){
				$items_loaded = Mage::helper('megamenu')->getParents( $params );
				if( is_array($items_loaded) && count($items_loaded) ){
					foreach ($items_loaded as $item) {
						$opt = new stdClass();
						$opt->value = $item['value'];
						$opt->label = $item['label'];
						$output->items[] = $opt;
					}
				} else {
					$output->success = false;
				}
			}
			$response->setHeader('Content-Type', 'application/x-json');
			$response->setBody( Mage::helper('megamenu')->jsonEncode($output) );
		}
	}
	
	public function ajaxGetChildrenAction(){
		$response = $this->getResponse();
		if ($params = Mage::app()->getRequest()->getParams()) {
			$output = (object)array( 'success' => true, 'items' => array() );
			if( $params['parent_id'] ){
				$items_loaded = Mage::helper('megamenu')->getChildren( $params );
				if( is_array($items_loaded) && count($items_loaded) ){
					foreach ($items_loaded as $item) {
						$opt = new stdClass();
						$opt->value = $item['value'];
						$opt->label = $item['label'];
						$output->items[] = $opt;
					}
				} else {
					$output->success = false;
				}
			}
			$response->setHeader('Content-Type', 'application/x-json');
			$response->setBody( Mage::helper('megamenu')->jsonEncode($output) );
		}
	}
	
	public function ResetAction(){
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$query = "DELETE FROM core_resource WHERE code = 'megamenu_setup';";
		$writeConnection->query($query);
		$this->getResponse()->setRedirect( Mage::getBaseUrl() );
	}
	
	
	public function rebuildAction(){
		$position_id = $this->getRequest()->getParam('position');
		if ($position_id){
			$posi = Mage::getModel('megamenu/position')->load($position_id);
			$root = Mage::getModel('megamenu/menu')->load($posi->getRootId());
			$root->rebuild();
		}
		$this->getResponse()->setRedirect( Mage::getBaseUrl() );
	}
}