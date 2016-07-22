<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Widget_Helper extends Mage_Widget_Block_Adminhtml_Widget_Options {

	protected $_debug = 0;
	
	public function init( $fieldset, $form ){
		$this->_translationHelper = Mage::helper('slider');
		return $this->setForm( $form ) && $this->setFieldset( $fieldset );
	}
	
	/**
	 * Add fields to main fieldset based on specified widget type
	 *
	 * @return Mage_Adminhtml_Block_Widget_Form
	 */
	public function addFields() {
		// get configuration node and translation helper
		$module = 'slider';
		$this->_translationHelper = Mage::helper( $module ? $module : 'widget');
		foreach( $this->getParameters() as $parameter ) {
			$this->_addField( $parameter );
		}
		
		return $this;
	}
	
	public function addField( $parameter ){
		return $this->_addField( $parameter );
	}
	
	public function getMainFieldset()
	{
		if($this->_getData('main_fieldset') instanceof Varien_Data_Form_Element_Fieldset) {
			return $this->_getData('main_fieldset');
		}
		$fieldset = $this->getFieldset();
		$this->setMainFieldsetHtmlId($fieldset->getId());
		$this->setData('main_fieldset', $fieldset);
	
		// add dependence javascript block
		$block = Mage::getSingleton('core/layout')->createBlock('adminhtml/widget_form_element_dependence');
		$this->setChild('form_after', $block);
	
		return $fieldset;
	}
	

	/**
	 * Add field to Options form based on parameter configuration
	 *
	 * @param Varien_Object $parameter
	 * @return Varien_Data_Form_Element_Abstract
	 */
	protected function _addField($parameter)
	{
		$this->_debug && var_dump( '-------- START DEBUG -----------', $parameter );
	
		$form = $this->getForm();
		$fieldset = $this->getMainFieldset(); //$form->getElement('options_fieldset');
	
		// prepare element data with values(either from request of from default values)
		$fieldName = $parameter->getKey();
		$data = array(
				'name'      => $fieldName,
				'label'     => $this->_translationHelper->__($parameter->getLabel()),
				'required'  => $parameter->getRequired(),
				'class'     => 'widget-option ' . $fieldName,
				'note'      => $this->_translationHelper->__($parameter->getDescription()),
		);
	
		if($values = $this->getWidgetValues()) {
			$data['value'] =(isset($values[$fieldName]) ? $values[$fieldName] : '');
		}
		else {
			$data['value'] = $parameter->getValue();
			//prepare unique id value
			if($fieldName == 'unique_id' && $data['value'] == '') {
				$data['value'] = md5(microtime(1));
			}
		}
	
		// prepare element dropdown values
		if($values  = $parameter->getValues()) {
			// dropdown options are specified in configuration
			$data['values'] = array();
			foreach($values as $option) {
				$data['values'][] = array(
						'label' => $this->_translationHelper->__($option['label']),
						'value' => $option['value']
				);
			}
		}
		// otherwise, a source model is specified
		elseif($sourceModel = $parameter->getSourceModel()) {
			$data['values'] = Mage::getModel($sourceModel)->toOptionArray();
		}
	
		// prepare field type or renderer
		$fieldRenderer = null;
		$fieldType = $parameter->getType();
		// hidden element
		if(!$parameter->getVisible()) {
			$fieldType = 'hidden';
		}
		// just an element renderer
		elseif(false !== strpos($fieldType, '/')) {
			$fieldRenderer = $this->getLayout()->createBlock($fieldType);
			$fieldType = $this->_defaultElementType;
		}
		$this->_debug && var_dump('Field ID:', $this->getMainFieldsetHtmlId() . '_' . $fieldName, 'Field Type:', $fieldType, 'Data:', $data );
		// instantiate field and render html
		$field = $fieldset->addField($this->getMainFieldsetHtmlId() . '_' . $fieldName, $fieldType, $data);
		if($fieldRenderer) {
			$field->setRenderer($fieldRenderer);
		}
	
		// extra html preparations
		if($helper = $parameter->getHelperBlock()) {
			$this->_debug && var_dump('Helper Data:', $helper->getData(), 'Fieldset ID:', $fieldset->getId() );
			$helperBlock = Mage::getSingleton('core/layout')->createBlock($helper->getType(), '', $helper->getData());
			$this->_debug && var_dump( 'Helper :', get_class($helperBlock) );
			if($helperBlock instanceof Varien_Object) {
				$helperBlock->setConfig($helper->getData())
				->setFieldsetId($fieldset->getId())
				->setTranslationHelper($this->_translationHelper)
				->prepareElementHtml($field);
			}
		}
	
		// dependencies from other fields
		$dependenceBlock = $this->getChild('form_after');
		$this->_debug && var_dump( get_class($dependenceBlock) );
		$dependenceBlock->addFieldMap($field->getId(), $fieldName);
		if($parameter->getDepends()) {
			foreach($parameter->getDepends() as $from => $row) {
				$values = isset($row['values']) ? array_values($row['values']) :(string)$row['value'];
				$dependenceBlock->addFieldDependence($fieldName, $from, $values);
			}
		}
	
		return $field;
	}
}