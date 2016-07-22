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
 
class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Weight {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'lb', 'label'=> Mage::helper('adminhtml')->__('Pounds (lb)'));
		$type[] = array('value'=>'oz', 'label'=> Mage::helper('adminhtml')->__('Ounces (oz)'));				
		$type[] = array('value'=>'g',  'label'=> Mage::helper('adminhtml')->__('Grams (g)'));				
		$type[] = array('value'=>'kg', 'label'=> Mage::helper('adminhtml')->__('Kilograms (kg)'));				
		return $type;		
	}
	
}