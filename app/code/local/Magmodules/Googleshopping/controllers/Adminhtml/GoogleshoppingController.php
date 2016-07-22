<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Googleshopping
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Googleshopping_Adminhtml_GoogleshoppingController extends Mage_Adminhtml_Controller_Action {

	public function generateManualAction($storeId = '') {	
		if(Mage::getStoreConfig('googleshopping/general/enabled')) {

			$results = array();
			$html = '';
		
			if(!$storeId) {
				$storeIds = Mage::helper('googleshopping')->getStoreIds(); 		
				foreach($storeIds as $storeId) {
					if(Mage::getStoreConfig('googleshopping/generate/enabled', $storeId)) {
						$results[] = Mage::getModel("googleshopping/googleshopping")->generateFeed($storeId);						
					}	
				}							
			} else {
				$results[] = Mage::getModel("googleshopping/googleshopping")->generateFeed($storeId);
			}
		
			foreach($results as $result) {
				$html .= '<tr><td>' . $result['shop'] . '</td><td><a href="' . $result['url'] . '">' . $result['url'] . '</a><br/>Date: ' . $result['date'] . ' - Products: ' . $result['qty'] . ' - Time: ' . $result['time'] . '</td></tr>';
			}

			if($html) {
				$html_header = '<div class="grid"><table cellpadding="0" cellspacing="0" class="border"><tbody><tr class="headings"><th>Code</th><th>Url</th></tr>';
				$html_footer = '</tbody></table></div>';
				$html = $html_header . $html . $html_footer;			
				$config = new Mage_Core_Model_Config();
				$config->saveConfig('googleshopping/generate/feeds', $html, 'default', 0);
				Mage::app()->getCacheInstance()->cleanType('config');
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('googleshopping')->__('Generated'));
			}
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('googleshopping')->__('Please enable the extension before generating the xml'));		
		}    	
        $this->_redirect('adminhtml/system_config/edit/section/googleshopping');
    } 
    

  	public function removeAttributesAction() {	
  		
  		$i = 0;
	  	$model = Mage::getResourceModel('catalog/setup','catalog_setup');
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
	
		if($googleshopping_exclude = $eavAttribute->getIdByCode('catalog_product', 'googleshopping_exclude')) {
			$model->removeAttribute('catalog_product','googleshopping_exclude');
			$i++;
		}
		
		if($googleshopping_condition = $eavAttribute->getIdByCode('catalog_product', 'googleshopping_condition')) {
			$model->removeAttribute('catalog_product','googleshopping_condition');		
			$i++;
		}
		
		if($googleshopping_category = $eavAttribute->getIdByCode('catalog_category', 'googleshopping_category')) {
			$model->removeAttribute('catalog_category','googleshopping_category');		
			$i++;			
		}
		
		if($i > 0) {
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('googleshopping')->__('Succesfully deleted %s attributes', $i));		
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('googleshopping')->__('No Attributes found!'));				
		}

        $this->_redirect('adminhtml/system_config/edit/section/magmodules_core');

  	}

  	public function installAttributesAction() {	
  		
  		$i = 0;
		$model = Mage::getResourceModel('catalog/setup','catalog_setup');
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
	
		if(!$eavAttribute->getIdByCode('catalog_product', 'googleshopping_exclude')) {
			$data = array(
				'group'        					=> 'Google Shopping',
				'input'         				=> 'select',
				'type'          				=> 'int',
				'source' 						=> 'eav/entity_attribute_source_boolean',
				'label'        					=> 'Exclude for Google Shopping',          
				'visible'       				=> 1,   
				'required'      				=> 0,
				'user_defined' 					=> 1,
				'searchable' 					=> 0,
				'filterable' 					=> 0,
				'comparable'    				=> 0,
				'used_in_product_listing'		=> 1,
				'visible_on_front' 				=> 0,
				'visible_in_advanced_search'  	=> 0,
				'is_html_allowed_on_front' 		=> 0,
				'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,     
			);
			$model->addAttribute('catalog_product','googleshopping_exclude', $data);
			$model->addAttributeToSet('catalog_product', 'Default', 'Google Shopping', 'googleshopping_exclude');
			$i++;	
		}

		if(!$eavAttribute->getIdByCode('catalog_product', 'googleshopping_condition')) {
			$data = array(
				'group'        					=> 'Google Shopping',
				'input'         				=> 'select',
				'type'          				=> 'int',   
				'backend'           			=> 'eav/entity_attribute_backend_array',
				'option' 						=> array('value' => array('new' => array('New'), 'refurbished' => array('Refurbished'), 'used' => array('Used'))),
				'default'						=> 'new',
				'label'        					=> 'Product Condition',          
				'visible'       				=> 1,   
				'required'      				=> 0,
				'user_defined' 					=> 1,
				'searchable' 					=> 0,
				'filterable' 					=> 0,
				'comparable'    				=> 0,
				'visible_on_front' 				=> 0,
				'visible_in_advanced_search'  	=> 0,
				'is_html_allowed_on_front' 		=> 0,
				'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,       
			);
			$model->addAttribute('catalog_product','googleshopping_condition', $data);
			$model->addAttributeToSet('catalog_product', 'Default', 'Google Shopping', 'googleshopping_condition');
			$i++;
		}

		if(!$eavAttribute->getIdByCode('catalog_category', 'googleshopping_category')) {
			$data = array(
				'group' 						=> 'Feeds',
				'input' 						=> 'text',
				'type' 							=> 'varchar',
				'label' 						=> 'Google Product Category',
				'required' 						=> false,
				'user_defined' 					=> true,
				'visible'						=> true,
				'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,	      
			);
			$model->addAttribute('catalog_category','googleshopping_category', $data);
			$i++;		
		}
		
		if($i > 0) {
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('googleshopping')->__('Succesfully installed %s attributes', $i));		
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('googleshopping')->__('No Attributes installed'));				
		}

        $this->_redirect('adminhtml/system_config/edit/section/magmodules_core');

  	}

}