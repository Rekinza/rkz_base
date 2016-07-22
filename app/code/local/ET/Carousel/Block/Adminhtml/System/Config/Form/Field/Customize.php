<?php
/**
 * @package ET_Carousel
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Carousel_Block_Adminhtml_System_Config_Form_Field_Customize extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function __construct () {
    	parent::__construct();
    	
        $this->addColumn('name', array(
            'label' => Mage::helper('adminhtml')->__('Title '),
            'style' => 'width:120px',
        ));
        $this->addColumn('url', array(
            'label' => Mage::helper('adminhtml')->__('URL'),
            'style' => 'width:120px',
        ));
        $this->addColumn('image', array(
            'label' => Mage::helper('adminhtml')->__('Image'),
            'style' => 'width:120px'
        ));
        $this->addColumn('short_description', array(
            'label' => Mage::helper('adminhtml')->__('Short Description'),
            'style' => 'width:120px',
        ));
        
    }
	
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null; // doh, the object is used as singleton!
        $html ='<div id="'.$element->getHtmlId().'">'.$html.'</div>';
		return $html;
    }
}