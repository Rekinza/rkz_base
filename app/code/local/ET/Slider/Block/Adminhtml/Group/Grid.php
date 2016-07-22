<?php
/**
 * @package ET_Slider
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Slider_Block_Adminhtml_Group_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		$this->setId('group_grid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession( true );
	}
	
	protected function _prepareCollection() {
		$collection = Mage::getModel('slider/group')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	public function addIdFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.id', array('eq'=>$column->getFilter()->getValue()));
	}
	public function addTitleFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.title', array('like'=>'%'.$column->getFilter()->getValue().'%'));
	}
	public function addStateFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.state', array('eq'=>$column->getFilter()->getValue()));
	}
	protected function _prepareColumns() {
		$this->addColumn('id', array(
				'header' => Mage::helper('slider')->__('ID'),
				'align' => 'right',
				'width' => '50px',
				'index' => 'id',
				'filter_condition_callback' => array($this, 'addIdFilter'),
				'sortable' => true
		));

		$this->addColumn('title', array(
				'header' => Mage::helper('slider')->__('Title'),
				'index' => 'title',
				'filter_condition_callback' => array($this, 'addTitleFilter'),
				'sortable'  => true,
				'renderer'  => 'slider/adminhtml_group_renderer_edit',
		));

		$this->addColumn('state', array(
				'header' => Mage::helper('slider')->__('State'),
				'width' => '100px',
				'index' => 'state',
				'type' => 'options',
				'options' => ET_Slider_Model_System_Config_Source_State::getOptionArray(),
				'filter_condition_callback' => array($this, 'addStateFilter'),
				'sortable'  => false,
		));

		$this->addColumn('action', array(
				'header' => Mage::helper('slider')->__('Action'),
				'width' => '100',
				'type' => 'action',
				'getter' => 'getId',
				'actions' => array(
					array(
						'caption' => Mage::helper('slider')->__('Edit'),
						'url' => array(
							'base' => '*/*/edit'
						),
						'field' => 'id'
					)
				),
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'is_system' => true
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('slider')->__('CSV') );
		$this->addExportType('*/*/exportXml', Mage::helper('slider')->__('XML') );

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('group_param');

		$this->getMassactionBlock()->addItem('delete', array(
				'label' => Mage::helper('slider')->__('Delete'),
				'url' => $this->getUrl('*/*/massDelete'),
				'confirm' => Mage::helper('slider')->__('Are you sure?')
		));

		$states = Mage::getSingleton('slider/system_config_source_state')->getOptionArray();

		array_unshift($states, array('label'=>'', 'value'=>''));

		$this->getMassactionBlock()->addItem('state', array(
				'label' => Mage::helper('slider')->__('Change State'),
				'url' => $this->getUrl('*/*/massState', array(
						'_current' => true
				)),
				'additional' => array(
						'visibility' => array(
								'name' => 'state',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => Mage::helper('slider')->__('State'),
								'values' => $states
						)
				)
		));
		return $this;
	}
	
	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array(
				'id' => $row->getId()
		));
	}
}