<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Block_Adminhtml_System_Config_Form_Field_GoogleFonts extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct()
    {
        $this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Font Name'),
            'style' => 'width:120px',
        ));
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Attribute'),
            'style' => 'width:220px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Google Font');
        parent::__construct();
    }
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $html = parent::_getElementHtml($element);
        return '<div id="'.$element->getId().'">' . $html . '</div>';
    }
}
