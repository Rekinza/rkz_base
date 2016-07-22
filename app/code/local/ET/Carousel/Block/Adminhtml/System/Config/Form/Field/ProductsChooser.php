<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Block_Adminhtml_System_Config_Form_Field_ProductsChooser extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    /**
     * Override method to output our custom HTML with JavaScript
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element) {
        $btn_trigger = '<button id="' . $element->getHtmlId() . '_trigger" class="scalable" onclick="return getProductChooser(\'' . $element->getHtmlId() . '\', \'' . $this->_getProductsChooserURL($element) . '\');"> ...</button>';
        
        $html = '<input id="' . $element->getHtmlId() . '" name="' . $element->getName() . '" style="width: 249px;" value="' . $element->getEscapedValue() . '" ' . $element->serialize($element->getHtmlAttributes()) . '/>' . $btn_trigger;
        $html .= '<div id="' . $element->getHtmlId() . '_chooser"></div>';
        
        $html .= $element->getAfterElementHtml();
        return $html;
    }
    
    protected function _getProductsChooserURL (Varien_Data_Form_Element_Abstract $element) {
        return Mage::getUrl(
            'adminhtml/promo_widget/chooser/attribute/sku/form/ProductsChooser["' . $element->getHtmlId() . '"]', array('_secure' => Mage::app()->getStore()->isAdminUrlSecure())
        ) . '?isAjax=true';
    }
    
}