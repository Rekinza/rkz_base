<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Block_Adminhtml_System_Config_Form_Field_ImageSelect
	extends Mage_Adminhtml_Block_System_Config_Form_Field {
    
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        // $this->setStyle('display:none;');
        $this->setName($element->getName());
        $this->setClass($element->getClass());
        $html = '';
        $value = $element->getValue();
        if ($values = $element->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html .= '<div style="height:0; display: block; clear: both;"><div>';
        $this->_once_style;
        if (!$this->getData('_once_style')){
            $html .= '<style>
                #'.$element->getId().' label.radio{
                    float: left;
                    margin: 0 5px 5px 0;
                }
                #'.$element->getId().' label.radio>input{
                    display: none !important;
                }
                #'.$element->getId().' label.radio>img{
                    width: 48px; height: 36px;
                    border: 2px solid transparent;
                    cursor: pointer;
                    display: block;
                }
                #'.$element->getId().' label.radio>input:checked+img{
                    border-color: #909090;
                }
                </style>';
            $this->getData('_once_style', true);
        }
        return '<div id="'.$element->getId().'">'.$html.'</div>';
    }
    
    protected function _optionToHtml($option, $selected) {
        $element = $this->getElement();
        $label = '<label class="radio"> %s '.$option['label'].'</label>';
        $radio = '<input type="radio"'.$this->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $escape_value = htmlspecialchars($option['value'], ENT_COMPAT);
            $radio.= 'value="'.$escape_value.'"  id="'.$element->getHtmlId().$option['value'].'"';
            if ($option['value'] == $selected) {
                $radio.= ' checked="checked"';
            }
            $radio.= ' />';
        } else if ($option instanceof Varien_Object) {
            $radio.= 'id="'.$element->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
            if (in_array($option->getValue(), $selected)) {
                $radio.= ' checked="checked"';
            }
            $radio.= ' />';
        }
        return $this->__($label, $radio);
    }
}
