<?php 
/**
 * Magmodules.eu
 * http://www.magmodules.eu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category    Magmodules
 * @package     Magmodules_Sortbydate
 * @author      Magmodules <info@magmodules.eu)
 * @copyright   Copyright (c) 2014 (http://www.magmodules.eu)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Magmodules_Googleshopping_Block_Adminhtml_Render_Status extends Mage_Adminhtml_Block_System_Config_Form_Field {
 
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
       
		// CHECK IF ATTRIBUTE EXISTS
		$html = '';
		$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
		
		// googleshopping_exclude attribute
		if($googleshopping_exclude = $eavAttribute->getIdByCode('catalog_product', 'googleshopping_exclude')) {
			$googleshopping_exclude_status = Mage::helper('googleshopping')->__('installed');
		} else {
			$googleshopping_exclude_status = '<font color="red">' . Mage::helper('googleshopping')->__('not installed!') . '</font>';		
		}	
		
		// googleshopping_condition attribute		
		if($googleshopping_condition = $eavAttribute->getIdByCode('catalog_product', 'googleshopping_condition')) {
			$googleshopping_condition_status = Mage::helper('googleshopping')->__('installed');
		} else {
			$googleshopping_condition_status = '<font color="red">' . Mage::helper('googleshopping')->__('not installed!') . '</font>';
		}
		
		// googleshopping_category attribute
		if($googleshopping_condition = $eavAttribute->getIdByCode('catalog_category', 'googleshopping_category')) {		
			$googleshopping_category_status = Mage::helper('googleshopping')->__('installed');
		} else {
			$googleshopping_category_status = '<font color="red">' . Mage::helper('googleshopping')->__('not installed!') . '</font>';
		}
			
		$html .= Mage::helper('googleshopping')->__('Exlude Attribute') . ': <strong>' . $googleshopping_exclude_status . '</strong><br/>';
		$html .= Mage::helper('googleshopping')->__('Condition Attribute') . ': <strong>' . $googleshopping_condition_status . '</strong><br/>';
		$html .= Mage::helper('googleshopping')->__('Category Attribute') . ': <strong>' . $googleshopping_category_status . '</strong><br/>';

        return $html;
    }

}