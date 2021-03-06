<?php
/**
 * @package ET_Megamenu
 * @version 1.0.0
 * @copyright Copyright (c) 2014 EcomTheme. (http://www.ecomtheme.com)
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ET_Megamenu_Block_Adminhtml_Menu_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	public function __construct() {
		parent::__construct ();
		$this->setId ( 'menu_grid' );
		$this->setDefaultSort ( 'id' );
		$this->setDefaultDir ( 'ASC' );
		$this->setSaveParametersInSession ( true );
	}

	protected function _prepareCollection(){
		$tbl_position = Mage::getModel('megamenu/position')->getCollection()->getTable('position');
		$collection = Mage::getModel('megamenu/menu')->getCollection();
		$collection ->getSelect()
			->joinLeft(array('tbl_position' => $tbl_position),'tbl_position.id = main_table.position_id', array('position_name' => 'tbl_position.title'))
			->columns('CONCAT( REPEAT( "--  ", main_table.depth-1 ) , main_table.title) AS title')
			->where('main_table.lft > 0')
			->order('main_table.position_id')
			->order('main_table.lft')
		;
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	public function addPositionFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('tbl_position.title', array('eq'=>$column->getFilter()->getValue()));
	}
	
	public function addIdFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.id', array('eq'=>$column->getFilter()->getValue()));
	}
	
	public function addTitleFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.title', array('like'=>$column->getFilter()->getValue().'%'));
	}
	public function addDepthFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.depth', array('eq'=>$column->getFilter()->getValue()));
	}
	
	public function addStateFilter(Mage_Eav_Model_Entity_Collection_Abstract $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column) {
		$collection->addFieldToFilter('main_table.state', array('eq'=>$column->getFilter()->getValue()));
	}
	
	protected function _prepareColumns(){
		$this->addColumn('id', array(
				'header'    => Mage::helper('megamenu')->__('ID'),
				'align'     =>'right',
				'width'     => '50px',
				'index'     => 'id',
				'filter_condition_callback' => array($this, 'addIdFilter'),
				'sortable'  => false,
		));

		$this->addColumn('name', array(
				'header'    => Mage::helper('megamenu')->__('Name'),
				'align'     => 'left',
				'index'     => 'name',
				'filter_condition_callback' => array($this, 'addTitleFilter'),
				'sortable'  => false,
				'renderer'  => 'megamenu/adminhtml_menu_renderer_edit',
		));
		
		/*
		$this->addColumn('parent_id', array(
				'header'    => Mage::helper('megamenu')->__('Parent ID'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'parent_id',
				'sortable'  => false,
		));
		
		$this->addColumn('lft', array(
				'header'    => Mage::helper('megamenu')->__('LFT'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'lft',
				'sortable'  => false,
		));
		
		$this->addColumn('rgt', array(
				'header'    => Mage::helper('megamenu')->__('RGT'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'rgt',
				'sortable'  => false,
		));
		
		$this->addColumn('depth', array(
				'header'    => Mage::helper('megamenu')->__('Level'),
				'align'     =>'left',
				'width'     => '50px',
				'index'     => 'depth',
				'sortable'  => false,
		));
		*/
		
		$this->addColumn('position_name', array(
				'header'    => Mage::helper('megamenu')->__('Position Name'),
				'width'     => '150px',
				'index'     => 'position_name',
				'type'      => 'options',
				'options'   => ET_Megamenu_Model_System_Config_Source_Positions::getOptionArray(),
				'filter_condition_callback' => array($this, 'addPositionFilter'),
				'sortable'  => false,
		));
		
		$this->addColumn('state', array(
				'header'    => Mage::helper('megamenu')->__('State'),
				'align'     => 'left',
				'width'     => '80px',
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

	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('menu_param');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'    => Mage::helper('megamenu')->__('Delete'),
			 'url'      => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('megamenu')->__('Are you sure?')
		));

		$states = Mage::getSingleton('megamenu/system_config_source_state')->getOptionArray();

		array_unshift($states, array('label'=>'', 'value'=>''));
		
		$this->getMassactionBlock()->addItem('status', array(
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

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}