<?php

/**
 * Product attribute add/edit form options tab
 *
 * @category   BusinessKing
 * @package    BusinessKing_AttributeQuickAdd
 */
class BusinessKing_AttributeQuickAdd_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Options
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('attributequickadd/catalog/product/attribute/options.phtml');
    }
}
