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
 
$installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');
$installer->startSetup();

// Add New Product Attribute Group - Google Shoppinh
$attributeSetId = Mage::getModel('catalog/product')->getDefaultAttributeSetId();
$attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);          
$installer->addAttributeGroup('catalog_product', $attributeSet->getAttributeSetName(), 'Google Shopping', 1000);

// Add New Product Attribute - Yes/No googleshopping_enabled
$installer->addAttribute('catalog_product', 'googleshopping_exclude', array(
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
    'visible_on_front' 				=> 0,
    'visible_in_advanced_search'  	=> 0,
    'is_html_allowed_on_front' 		=> 0,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,     
));

// Add New Product Attribute - Condition
$installer->addAttribute('catalog_product', 'googleshopping_condition', array(
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
));
    
// Add New Category Attribute - Google Product Category
$installer->addAttribute('catalog_category', 'googleshopping_category', array(
	'group' 						=> 'Feeds',
	'input' 						=> 'text',
	'type' 							=> 'varchar',
	'label' 						=> 'Google Product Category',
	'required' 						=> false,
	'user_defined' 					=> true,
	'visible'						=> true,
	'global'						=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,	
));

$installer->endSetup();