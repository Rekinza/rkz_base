<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Position_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	public function __construct(){
		parent::__construct();
		$this->setId('position_grid');
		$this->setDefaultSort('id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('megamenu/position')->getCollection();
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
		// echo $column->getFilter()->getValue();die;
		$collection->addFieldToFilter('main_table.state', array('eq'=>$column->getFilter()->getValue()));
		// echo $collection->getSelect();die;
	}
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
				'header'    => Mage::helper('megamenu')->__('ID'),
				'align'     =>'right',
				'width'     => '50px',
				'index'     => 'id',
				'filter_condition_callback' => array($this, 'addIdFilter'),
				'sortable'  => true,
		));

		$this->addColumn('title', array(
				'header'    => Mage::helper('megamenu')->__('Title'),
				'align'     =>'left',
				'index'     => 'title',
				'filter_condition_callback' => array($this, 'addTitleFilter'),
				'sortable'  => true,
				'renderer'  => 'megamenu/adminhtml_position_renderer_edit',
		));

		$this->addColumn('state', array(
				'header'    => Mage::helper('megamenu')->__('State'),
				'align'     => 'left',
				'width'     => '100px',
				'index'     => 'state',
				'type'      => 'options',
				'options'   => ET_Megamenu_Model_System_Config_Source_State::getOptionArray(),
				'filter_condition_callback' => array($this, 'addStateFilter'),
				'sortable'  => false,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('megamenu')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('megamenu')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('position_param');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'    => Mage::helper('megamenu')->__('Delete'),
			 'url'      => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('megamenu')->__('Are you sure?')
		));

		$states = Mage::getSingleton('megamenu/system_config_source_state')->getOptionArray();

		array_unshift($states, array('label'=>'', 'value'=>''));

		$this->getMassactionBlock()->addItem('state', array(
			 'label'=> Mage::helper('megamenu')->__('Change State'),
			 'url'  => $this->getUrl('*/*/massState', array('_current'=>true)),
			 'additional' => array(
			 		'visibility' => array(
			 				'name' => 'state',
			 				'type' => 'select',
			 				'class' => 'required-entry',
			 				'label' => Mage::helper('megamenu')->__('State'),
			 				'values' => $states
					 )
			 )
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}