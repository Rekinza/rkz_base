<?php
class Msg_Msg91_Block_Adminhtml_Customnotification_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
	parent::__construct();
         $this->setId('customnotification_Grid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $resource = Mage::getSingleton('core/resource');
        $collection = Mage::getModel('msg91/customnotification')->getCollection();
	$this->setCollection($collection); 
	return parent::_prepareCollection();
    }
   
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => Mage::helper('msg91')->__('Id'),
            'width' => '40px',
            'index' => 'rule_id',
	    
        ));
	
	
      $this->addColumn('name', array(
          'header'    => Mage::helper('msg91')->__('Rule Name'),
	  'width'     => '150px',
          'align'     =>'left',
          'index'     => 'name'
      ));
      
      $this->addColumn('content', array(
          'header'    => Mage::helper('msg91')->__('Message'),
          'align'     =>'left',
          'index'     => 'content',
	  'width' =>'200px'
      ));

      $this->addColumn('is_active', array(
	'header'    => Mage::helper('msg91')->__('Status'),
	'align'     =>'left',
	'index'     => 'is_active',
	'width' =>'50px',
	'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              0 => 'Disabled',
          ),
      ));


	 $this->addColumn('action',
            array(
                'header'    => Mage::helper('msg91')->__('Action'),
                 'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('msg91')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
	
         return parent::_prepareColumns();
    }
       protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('rules');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('msg91')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('msg91')->__('Are you sure?')
        ));    
        return $this;
    }
    
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
  
}
