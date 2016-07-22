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
 
class Magmodules_Googleshopping_Model_Adminhtml_System_Config_Source_Producttype {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('No'));
		$type[] = array('value'=>'full', 'label'=> Mage::helper('adminhtml')->__('Full Category Path'));
		$type[] = array('value'=>'last', 'label'=> Mage::helper('adminhtml')->__('Only Deepest Categoy'));
		return $type;		
	}

}