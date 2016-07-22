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
 
class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Varcharattribute {

	protected $_ignore = array(
		'compatibility',
		'gallery',
		'installation',
		'language_support',
		'country_of_manufacture',
		'links_title',
		'current_version',
		'custom_design',
		'custom_layout_update',
		'gift_message_available',
		'image',
		'image_label',
		'media_gallery',
		'msrp_display_actual_price_type',
		'msrp_enabled',
		'options_container',
		'price_view',
		'page_layout',
		'samples_title',
		'sku_type',
		'tier_price',
		'url_key',
		'small_image',
		'small_image_label',
		'thumbnail',
		'thumbnail_label',
		'recurring_profile',
		'version_info',
		'meta_keyword',
		'meta_description',
	);

    public function toOptionArray(){
        $options = array();
		$options[] = array('value' => '', 'label' => Mage::helper('googleshopping')->__('-- none'));
        $entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getEntityTypeId();
        $attributes = Mage::getModel('eav/entity_attribute')->getCollection()->addFilter('entity_type_id', $entityTypeId)->setOrder('attribute_code', 'ASC');
        foreach ($attributes as $attribute){
			if($attribute->getBackendType() == 'varchar') {
				if($attribute->getFrontendLabel()) {
					if(!in_array($attribute->getAttributeCode(), $this->_ignore)) {
						$options[] = array('value'=> $attribute->getAttributeCode(), 'label'=> $attribute->getFrontendLabel());
					}
				}
			}
        }       
        return $options;
    }

}