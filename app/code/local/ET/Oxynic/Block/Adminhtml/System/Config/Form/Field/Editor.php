<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Block_Adminhtml_System_Config_Form_Field_Editor
	extends Mage_Adminhtml_Block_System_Config_Form_Field
    implements Varien_Data_Form_Element_Renderer_Interface {
	
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $element->setWysiwyg(true);
        $econfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $econfig->setHidden(false);
        $element->setConfig($econfig);
        return parent::_getElementHtml($element);
    }
}
