<?php
/**
 * @package ET_Oxynic
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Oxynic_Model_System_Config_Source_FontFamilies {
    
	public function toOptionArray(){
		return array(
			array('value' => '', 'label' => Mage::helper('oxynic')->__('No select')),
		    // Sans-Serif
		    array('value' => 'Arial, sans-serif', 'label' => Mage::helper('oxynic')->__('Arial, sans-serif')),
		    array('value' => 'Helvetica, sans-serif', 'label' => Mage::helper('oxynic')->__('Helvetica, sans-serif')),
		    array('value' => 'Gill Sans, sans-serif', 'label' => Mage::helper('oxynic')->__('Gill Sans, sans-serif')),
		    array('value' => 'Lucida, sans-serif', 'label' => Mage::helper('oxynic')->__('Lucida, sans-serif')),
		    array('value' => 'Helvetica Narrow, sans-serif', 'label' => Mage::helper('oxynic')->__('Helvetica Narrow, sans-serif')),
		    array('value' => 'sans-serif', 'label' => Mage::helper('oxynic')->__('sans-serif')),
		    // Serif
		    array('value' => 'Georgia, serif', 'label' => Mage::helper('oxynic')->__('Georgia, serif')),
		    array('value' => 'Times, serif', 'label' => Mage::helper('oxynic')->__('Times, serif')),
		    array('value' => 'Times New Roman, serif', 'label' => Mage::helper('oxynic')->__('Times New Roman, serif')),
		    array('value' => 'Palatino, serif', 'label' => Mage::helper('oxynic')->__('Palatino, serif')),
		    array('value' => 'Bookman, serif', 'label' => Mage::helper('oxynic')->__('Bookman, serif')),
		    array('value' => 'New Century Schoolbook, serif', 'label' => Mage::helper('oxynic')->__('New Century Schoolbook, serif')),
		    array('value' => 'serif', 'label' => Mage::helper('oxynic')->__('serif')),
		    // Monospace
		    array('value' => 'Andale Mono, monospace', 'label' => Mage::helper('oxynic')->__('Andale Mono, monospace')),
		    array('value' => 'Courier New, monospace', 'label' => Mage::helper('oxynic')->__('Courier New, monospace')),
		    array('value' => 'Courier, monospace', 'label' => Mage::helper('oxynic')->__('Courier, monospace')),
		    array('value' => 'Lucidatypewriter, monospace', 'label' => Mage::helper('oxynic')->__('Lucidatypewriter, monospace')),
		    array('value' => 'Fixed, monospace', 'label' => Mage::helper('oxynic')->__('Fixed, monospace')),
		    array('value' => 'monospace', 'label' => Mage::helper('oxynic')->__('monospace')),
		    // Cursive
		    array('value' => 'Comic Sans, Comic Sans MS, cursive', 'label' => Mage::helper('oxynic')->__('Comic Sans, Comic Sans MS, cursive')),
		    array('value' => 'Zapf Chancery, cursive', 'label' => Mage::helper('oxynic')->__('Zapf Chancery, cursive')),
		    array('value' => 'Coronetscript, cursive', 'label' => Mage::helper('oxynic')->__('Coronetscript, cursive')),
		    array('value' => 'Florence, cursive', 'label' => Mage::helper('oxynic')->__('Florence, cursive')),
		    array('value' => 'Parkavenue, cursive', 'label' => Mage::helper('oxynic')->__('Parkavenue, cursive')),
		    array('value' => 'cursive', 'label' => Mage::helper('oxynic')->__('cursive')),
		    // Fantasy
		    array('value' => 'Impact, fantasy', 'label' => Mage::helper('oxynic')->__('Impact, fantasy')),
		    array('value' => 'Arnoldboecklin, fantasy', 'label' => Mage::helper('oxynic')->__('Arnoldboecklin, fantasy')),
		    array('value' => 'Oldtown, fantasy', 'label' => Mage::helper('oxynic')->__('Oldtown, fantasy')),
		    array('value' => 'Blippo, fantasy', 'label' => Mage::helper('oxynic')->__('Blippo, fantasy')),
		    array('value' => 'Brushstroke, fantasy', 'label' => Mage::helper('oxynic')->__('Brushstroke, fantasy')),
		    array('value' => 'fantasy', 'label' => Mage::helper('oxynic')->__('fantasy'))
		);
	}
}
