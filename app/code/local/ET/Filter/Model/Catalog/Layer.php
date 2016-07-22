<?php
/**
 * @package ET_Filter
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Filter_Model_Catalog_Layer extends Mage_Catalog_Model_Layer{

    public function getFilterableAttributes(){
        $collection = parent::getFilterableAttributes();

        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Attribute_Collection) {
            $attrUrlKeyModel = Mage::getResourceModel('filter/attribute_urlkey');
            $attrUrlKeyModel->preloadAttributesOptions($collection);
        }

        return $collection;
    }

}